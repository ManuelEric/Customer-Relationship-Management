{{-- 
    CASE 1: When isset user && agreement > 0        [isset($user) && count($user->user_subjects) > 0]
    CASE 2: When agreement have error validation    [old('count_subject') !== null]
    CASE 3: Create new agreement                    [else]

    Trigger 1: Add agreement
    Trigger 2: Add role (External Mentor, Tutor, Editor, Individual Professional)
--}}




<p>{{$a}}</p>