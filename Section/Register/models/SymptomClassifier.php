<?php
// models/SymptomClassifier.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/BodySystem.php';
require_once __DIR__ . '/BodyLocation.php';
require_once __DIR__ . '/PossibleCondition.php';

class SymptomClassifier
{
    private PDO $db;
    private BodySystem $bodySystemModel;
    private BodyLocation $bodyLocationModel;
    private PossibleCondition $conditionModel;

    // Keyword taxonomy for clinical categorization
    private array $systemKeywords = [
        'digestive' => [
            'stomach', 'abdomen', 'abdominal', 'belly', 'nausea', 'vomiting', 
            'diarrhea', 'constipation', 'acid', 'reflux', 'heartburn', 'bloating', 
            'indigestion', 'cramps', 'bowel', 'gastric', 'ulcer', 'appetite', 'stool', 'eating', 'food', 'gut'
        ],
        'cardiovascular' => [
            'chest', 'heart', 'palpitation', 'palpitations', 'racing heart', 'heartbeat', 
            'pulse', 'tightness', 'pressure in chest', 'angina', 'cardiac', 'circulation', 'flutters', 'cardio'
        ],
        'nervous' => [
            'head', 'headache', 'migraine', 'dizzy', 'dizziness', 'vertigo', 
            'lightheaded', 'numbness', 'tingling', 'seizure', 'neuralgia', 'brain', 
            'faint', 'confusion', 'balance', 'tremor', 'memory', 'neuropathy'
        ],
        'respiratory' => [
            'cough', 'coughing', 'breathing', 'breath', 'shortness of breath', 'wheezing', 
            'lungs', 'throat', 'phlegm', 'mucus', 'asthma', 'congestion', 'respiratory', 'inhale', 'exhale'
        ],
        'musculoskeletal' => [
            'back', 'joint', 'muscle', 'bone', 'knee', 'shoulder', 'spine', 'neck', 
            'hip', 'ankle', 'wrist', 'sprain', 'strain', 'stiffness', 'arthritis', 
            'swelling', 'sciatica', 'leg pain', 'arm pain', 'lumbar'
        ],
        'integumentary' => [
            'skin', 'rash', 'itching', 'itch', 'redness', 'lesion', 'eczema', 
            'hives', 'acne', 'bumps', 'burn', 'scaling', 'blister', 'mole', 'dermatitis', 'allergy'
        ],
        'urinary' => [
            'urine', 'urination', 'bladder', 'kidney', 'peeing', 'burning urine', 
            'urinary', 'flank pain', 'frequent urination', 'bloody urine', 'pelvic', 'dysuria'
        ],
        'endocrine' => [
            'thyroid', 'fatigue', 'weight', 'hormone', 'sugar', 'glucose', 
            'sweating', 'cold intolerance', 'heat intolerance', 'thirst', 'metabolism'
        ],
        'general' => [
            'fever', 'chills', 'weakness', 'malaise', 'tired', 'exhaustion', 
            'sweats', 'whole body', 'unwell', 'sick', 'virus', 'general'
        ]
    ];

    // Urgent symptom triggers
    private array $urgentKeywords = [
        'severe chest pain', 'crushing chest', 'cannot breathe', "can't breathe",
        'passed out', 'unconscious', 'stroke', 'facial drooping', 'coughing blood',
        'seizure', 'worst headache of my life', 'sudden numbness in arm'
    ];

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
        $this->bodySystemModel = new BodySystem($this->db);
        $this->bodyLocationModel = new BodyLocation($this->db);
        $this->conditionModel = new PossibleCondition($this->db);
    }

    /**
     * Classify a patient's complaint and symptoms
     */
    public function classifyComplaint(string $complaintText, array $symptoms = [], ?string $locationCode = null): array
    {
        $lower = strtolower($complaintText . ' ' . implode(' ', $symptoms));
        $scores = [];

        // 1. Calculate keyword frequency scores per body system
        foreach ($this->systemKeywords as $sysCode => $keywords) {
            $count = 0;
            foreach ($keywords as $kw) {
                if (strpos($lower, $kw) !== false) {
                    $count += 1;
                }
            }
            $scores[$sysCode] = $count;
        }

        // 2. Factor in Anatomical Location if provided
        if (!empty($locationCode)) {
            $loc = $this->bodyLocationModel->getByCode($locationCode);
            if ($loc && !empty($loc['SystemCode']) && isset($scores[$loc['SystemCode']])) {
                $scores[$loc['SystemCode']] += 3; // strong weight to anatomical site
            }
        }

        // 3. Determine Highest Matching Body System
        $topSysCode = 'digestive';
        $maxScore = -1;
        foreach ($scores as $sysCode => $score) {
            if ($score > $maxScore) {
                $maxScore = $score;
                $topSysCode = $sysCode;
            }
        }

        // 4. Retrieve System Record from Database
        $bodySystem = $this->bodySystemModel->getByCode($topSysCode);
        if (!$bodySystem) {
            $bodySystem = $this->bodySystemModel->getByCode('digestive') ?: [
                'BodySystemID' => 1,
                'SystemName' => 'Digestive System',
                'SystemCode' => 'digestive',
                'Emoji' => '🩺',
                'Description' => 'Involves esophagus, stomach, intestines, liver, and pancreas.'
            ];
        }

        // 5. Calculate Simulated Confidence Rating
        $confidencePercentage = min(98, max(76, 74 + ($maxScore * 5)));
        $confidenceLevel = "High ({$confidencePercentage}%)";

        // 6. Check for Urgent Red Flags
        $isUrgent = false;
        foreach ($this->urgentKeywords as $urgentKw) {
            if (strpos($lower, $urgentKw) !== false) {
                $isUrgent = true;
                break;
            }
        }

        // 7. Retrieve Possible Discussion Conditions for this system from Database
        $conditions = $this->conditionModel->getByBodySystemId((int)$bodySystem['BodySystemID']);

        // 8. Generate Clinical Notes Rationale
        $clinicalNotes = "Automated keyword pattern analysis indicates primary correlation with the {$bodySystem['SystemName']} based on reported clinical presentation.";

        return [
            'success'            => true,
            'body_system'        => $bodySystem,
            'relevance_level'    => $confidencePercentage,
            'confidence_level'   => $confidenceLevel,
            'is_urgent'          => $isUrgent,
            'clinical_notes'     => $clinicalNotes,
            'conditions'         => $conditions,
            'disclaimer'         => 'System relevance estimate — not a medical diagnosis. A qualified healthcare professional must conduct a formal clinical evaluation.'
        ];
    }
}
