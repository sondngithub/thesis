<?php


namespace App\Services;


use App\Criteria;
use App\Evaluation;
use App\Course;
use App\Services\Interfaces\EvaluationServiceInterface;
use App\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationService extends Service implements EvaluationServiceInterface
{
    public function storeEvaluation(Request $request)
    {
        $answers = $request->except([Evaluation::COL_COURSE_ID, '_token', Evaluation::COL_TYPE, Evaluation::COL_CRITERIA_TYPE]);

        $result = Evaluation::create([
            Evaluation::COL_USER_ID => Auth::id(),
            Evaluation::COL_COURSE_ID => $request->input(Evaluation::COL_COURSE_ID),
            Evaluation::COL_ANSWERS => $answers,
            Evaluation::COL_TYPE => $request->input(Evaluation::COL_TYPE),
            Evaluation::COL_CRITERIA_TYPE => $request->input(Evaluation::COL_CRITERIA_TYPE),
        ]);
        if ($result) {
            $this->calculateToPFR($request->input(Evaluation::COL_COURSE_ID));
        }

        return $result;
    }

    public function getCriteriaType($criteriaCode) {
        return Criteria::where(Criteria::COL_CODE, $criteriaCode)
            ->first()
            ->type
            ->id;
    }

    public function calculateToPFR($courseId) {
        $evaluations = Evaluation::with('user')
            ->where(Evaluation::COL_COURSE_ID, $courseId)
            ->where(Evaluation::COL_TYPE, Evaluation::TYPE_PFR)
            ->get();
        $results = [];
        $reliabilities = [];
        $criteria = [];
        $pfr = [];
        $sumReliabilityByCriteriaType = [];

        foreach ($evaluations as $evaluation) {
            $results[$evaluation->user_id] = $evaluation->answers;
            $reliabilities[$evaluation->user_id] = $evaluation->user->reliability;
            if (!isset($sumReliabilityByCriteriaType[$evaluation->criteria_type])) {
                $sumReliabilityByCriteriaType[$evaluation->criteria_type] = 0;
            }
            $sumReliabilityByCriteriaType[$evaluation->criteria_type] += $evaluation->user->reliability;
        }

        foreach ($results as $userId => $answers) {
            foreach ($answers as $criteriaCode => $answer) {
                if (!isset($criteria[$criteriaCode][Evaluation::AGREEMENT])) {
                    $criteria[$criteriaCode][Evaluation::AGREEMENT] = 0;
                }
                if (!isset($criteria[$criteriaCode][Evaluation::NEUTRAL])) {
                    $criteria[$criteriaCode][Evaluation::NEUTRAL] = 0;
                }
                if (!isset($criteria[$criteriaCode][Evaluation::DISAGREEMENT])) {
                    $criteria[$criteriaCode][Evaluation::DISAGREEMENT] = 0;
                }
                switch ((int)$answer) {
                    case Evaluation::AGREEMENT:
                        $criteria[$criteriaCode][Evaluation::AGREEMENT] += $reliabilities[$userId];
                        break;
                    case Evaluation::NEUTRAL:
                        $criteria[$criteriaCode][Evaluation::NEUTRAL] += $reliabilities[$userId];
                        break;
                    case Evaluation::DISAGREEMENT:
                        $criteria[$criteriaCode][Evaluation::DISAGREEMENT] += $reliabilities[$userId];
                        break;
                }
            }
        }

        foreach ($criteria as $criteriaCode => $memberships) {
            $criteriaType = $this->getCriteriaType($criteriaCode);
            foreach ($memberships as $key => $value) {
                $pfr[$criteriaCode][$key] = round($value/$sumReliabilityByCriteriaType[$criteriaType], 2);
            }
        }
        $this->savePFR($pfr, $courseId);
    }

    public function savePFR($pfr, $courseId) {
        return Course::findOrFail($courseId)->update([
            Course::COL_PFR => $pfr,
        ]);
    }

    public function getAvgEvaluation($courseId) {
        $pfr = Course::findOrFail($courseId)->pfr;
        $sr = [];
        if ($pfr) {
            foreach ($pfr as $criteriaCode => $pfs) {
                $sr[$criteriaCode] = $pfs[Evaluation::AGREEMENT] - $pfs[Evaluation::DISAGREEMENT]*(1 - $pfs[Evaluation::AGREEMENT] - $pfs[Evaluation::NEUTRAL] - $pfs[Evaluation::DISAGREEMENT]);
            }
        }

        return $sr;
    }

    public function countEvaluation($courseId) {
        return Evaluation::where(Evaluation::COL_COURSE_ID, $courseId)
            ->count();
    }

    public function getEvaluation($courseId, $userId) {
        $usingTypeId = Type::select(Type::COL_ID)
            ->where(Type::COL_IS_USING, true)
            ->get();

        return Evaluation::where(Evaluation::COL_COURSE_ID, $courseId)
            ->where(Evaluation::COL_USER_ID, $userId)
            ->whereIn(Evaluation::COL_CRITERIA_TYPE, $usingTypeId)
            ->get();
    }
}
