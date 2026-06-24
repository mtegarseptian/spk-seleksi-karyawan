<?php

namespace App\Services;

class RandomForestService
{
    protected int $nTrees;
    protected int $maxDepth;
    protected int $minSamplesSplit;
    protected int $bootstrapSize;
    protected int $maxFeaturesPerSplit;

    protected array $trees = [];
    protected array $featureImportance = [];
    protected array $featureNames = [];

    public function __construct(
        int $nTrees = 15,
        int $maxDepth = 6,
        int $minSamplesSplit = 20,
        int $bootstrapSize = 1200,
        int $maxFeaturesPerSplit = 3
    ) {
        $this->nTrees = $nTrees;
        $this->maxDepth = $maxDepth;
        $this->minSamplesSplit = $minSamplesSplit;
        $this->bootstrapSize = $bootstrapSize;
        $this->maxFeaturesPerSplit = $maxFeaturesPerSplit;
    }

    public function train(array $samples, array $labels, array $featureNames): void
    {
        $this->featureNames = $featureNames;
        $this->trees = [];
        $this->featureImportance = array_fill_keys($featureNames, 0.0);

        $total = count($samples);

        for ($t = 0; $t < $this->nTrees; $t++) {
            // Bootstrap sampling (with replacement) -> ciri khas Random Forest
            $bootSamples = [];
            $bootLabels = [];
            $size = min($this->bootstrapSize, $total);

            for ($k = 0; $k < $size; $k++) {
                $idx = random_int(0, $total - 1);
                $bootSamples[] = $samples[$idx];
                $bootLabels[] = $labels[$idx];
            }

            $this->trees[] = $this->buildTree($bootSamples, $bootLabels, 0);
        }

        $totalImportance = array_sum($this->featureImportance);
        if ($totalImportance > 0) {
            foreach ($this->featureImportance as $f => $val) {
                $this->featureImportance[$f] = $val / $totalImportance;
            }
        }
    }

    protected function buildTree(array $samples, array $labels, int $depth): DecisionTreeNode
    {
        $node = new DecisionTreeNode();
        $n = count($labels);
        $prob = $n > 0 ? array_sum($labels) / $n : 0;

        if ($depth >= $this->maxDepth || $n < $this->minSamplesSplit || $prob == 0 || $prob == 1) {
            $node->isLeaf = true;
            $node->probability = $prob;
            return $node;
        }

        $bestSplit = $this->findBestSplit($samples, $labels);

        if ($bestSplit === null) {
            $node->isLeaf = true;
            $node->probability = $prob;
            return $node;
        }

        [$featureIndex, $threshold, $leftIdx, $rightIdx, $gain] = $bestSplit;

        // catat feature importance (Gini gain dibobot jumlah sampel)
        $featureName = $this->featureNames[$featureIndex];
        $this->featureImportance[$featureName] += $gain * $n;

        $leftSamples  = array_map(fn ($i) => $samples[$i], $leftIdx);
        $leftLabels   = array_map(fn ($i) => $labels[$i], $leftIdx);
        $rightSamples = array_map(fn ($i) => $samples[$i], $rightIdx);
        $rightLabels  = array_map(fn ($i) => $labels[$i], $rightIdx);

        $node->featureIndex = $featureIndex;
        $node->threshold = $threshold;
        $node->left = $this->buildTree($leftSamples, $leftLabels, $depth + 1);
        $node->right = $this->buildTree($rightSamples, $rightLabels, $depth + 1);

        return $node;
    }

    protected function gini(array $labels): float
    {
        $n = count($labels);
        if ($n === 0) return 0;
        $p1 = array_sum($labels) / $n;
        $p0 = 1 - $p1;
        return 1 - ($p0 ** 2) - ($p1 ** 2);
    }

    protected function findBestSplit(array $samples, array $labels): ?array
    {
        $nFeatures = count($samples[0]);
        $n = count($samples);
        $parentGini = $this->gini($labels);

        // Random feature subset per split -> ciri khas Random Forest
        $allFeatures = range(0, $nFeatures - 1);
        shuffle($allFeatures);
        $candidateFeatures = array_slice($allFeatures, 0, min($this->maxFeaturesPerSplit, $nFeatures));

        $best = null;
        $bestGini = $parentGini;

        foreach ($candidateFeatures as $featureIndex) {
            $values = array_unique(array_map(fn ($s) => $s[$featureIndex], $samples));
            sort($values);

            $thresholds = $this->generateThresholds($values, 10);

            foreach ($thresholds as $threshold) {
                $leftIdx = [];
                $rightIdx = [];

                foreach ($samples as $i => $s) {
                    if ($s[$featureIndex] <= $threshold) {
                        $leftIdx[] = $i;
                    } else {
                        $rightIdx[] = $i;
                    }
                }

                if (count($leftIdx) === 0 || count($rightIdx) === 0) {
                    continue;
                }

                $leftLabels = array_map(fn ($i) => $labels[$i], $leftIdx);
                $rightLabels = array_map(fn ($i) => $labels[$i], $rightIdx);

                $weightedGini = (count($leftLabels) / $n) * $this->gini($leftLabels)
                    + (count($rightLabels) / $n) * $this->gini($rightLabels);

                if ($weightedGini < $bestGini) {
                    $bestGini = $weightedGini;
                    $gain = $parentGini - $weightedGini;
                    $best = [$featureIndex, $threshold, $leftIdx, $rightIdx, $gain];
                }
            }
        }

        return $best;
    }

    protected function generateThresholds(array $sortedValues, int $maxCandidates): array
    {
        $count = count($sortedValues);
        if ($count <= 1) return [];

        $thresholds = [];
        $step = max(1, (int) floor($count / $maxCandidates));

        for ($i = 0; $i < $count - 1; $i += $step) {
            $thresholds[] = ($sortedValues[$i] + $sortedValues[$i + 1]) / 2;
        }

        return $thresholds;
    }

    public function predictProba(array $sample): float
    {
        if (empty($this->trees)) return 0;

        $sum = 0;
        foreach ($this->trees as $tree) {
            $sum += $this->traverse($tree, $sample);
        }

        return $sum / count($this->trees);
    }

    protected function traverse(DecisionTreeNode $node, array $sample): float
    {
        if ($node->isLeaf) {
            return $node->probability;
        }

        if ($sample[$node->featureIndex] <= $node->threshold) {
            return $this->traverse($node->left, $sample);
        }

        return $this->traverse($node->right, $sample);
    }

    public function getFeatureImportance(): array
    {
        return $this->featureImportance;
    }

    public function exportModel(): array
    {
        return [
            'trees' => array_map(fn ($t) => $this->serializeNode($t), $this->trees),
            'feature_names' => $this->featureNames,
            'feature_importance' => $this->featureImportance,
        ];
    }

    protected function serializeNode(DecisionTreeNode $node): array
    {
        if ($node->isLeaf) {
            return ['leaf' => true, 'probability' => $node->probability];
        }

        return [
            'leaf' => false,
            'feature_index' => $node->featureIndex,
            'threshold' => $node->threshold,
            'left' => $this->serializeNode($node->left),
            'right' => $this->serializeNode($node->right),
        ];
    }

    public function importModel(array $data): void
    {
        $this->featureNames = $data['feature_names'];
        $this->featureImportance = $data['feature_importance'];
        $this->trees = array_map(fn ($t) => $this->deserializeNode($t), $data['trees']);
    }

    protected function deserializeNode(array $data): DecisionTreeNode
    {
        $node = new DecisionTreeNode();

        if ($data['leaf']) {
            $node->isLeaf = true;
            $node->probability = $data['probability'];
            return $node;
        }

        $node->featureIndex = $data['feature_index'];
        $node->threshold = $data['threshold'];
        $node->left = $this->deserializeNode($data['left']);
        $node->right = $this->deserializeNode($data['right']);

        return $node;
    }
}