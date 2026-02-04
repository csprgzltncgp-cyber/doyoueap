<?php

return [
    'close-case' => 'After closing case document, this will no longer be available. Please make a note of the number of sessions linked to the case, so that you could use this details for billing purposes later. Are you sure you want to continue closing this case?',
    'closeable-case' => 'The case can be closed, please finalize by pressing the \':BUTTON\' button!',

    'first_consultation_wos_warning' => 'At the end of the first session, don\'t forget to click on the WOS button to ask the client the WOS survey questions. Without filling out the questionnaire, it is not possible to add a second session to the case file!',
    'last_consultation_wos_warning' => 'At the end of the last session, don\'t forget to click on the WOS button to ask the client the WOS survey questions. Without filling out the questionnaire, it is not possible to close the case file!',

    'wos_instruction' => 'Please ask the client the following 6 questions and record their answers!',
    'wos_survey_warning' => 'Please fill out the questionnaire!',
    'wos_first_consultation_warning' => 'You did not fill out the WOS questionnaire after the first session!',
    'wos_case_close_warning' => 'You did not fill out the WOS questionnaire after the last session!',
    'wos_max_survey_warning' => 'You cannot fill out more WOS questionnaires for this case!',
    'invalid_wos_save' => 'You can only fill out the WOS questionnaire after the first and last sessions!',
    'wos_questions' => [
        1 => 'Have you missed work in the last 30 days?',
        2 => 'How often in the last 30 days did you not work due to personal problems? Include full workdays and partial workdays where you arrived late or left early. Please select the category that best describes the total time of your absence (if any):',
        3 => 'So far, it seems that my life is progressing well.',
        4 => 'I often can\'t wait to get to work and start the day.',
        5 => 'My personal problems have prevented me from concentrating on work.',
        6 => 'I dread going to work.',
    ],
    'wos_answers' => [
        1 => [
            1 => 'Not at all',
            2 => 'For a few days',
        ],
        2 => [
            1 => 'No absence',
            2 => 'Less than half a day of absence',
            3 => 'Absence between half and full day',
            4 => '1-3 days of absence',
            5 => 'More than 3 days of absence',
        ],
        3 => [
            1 => 'Strongly disagree',
            2 => 'Somewhat disagree',
            3 => 'Neutral',
            4 => 'Somewhat agree',
            5 => 'Strongly agree',
        ],
    ],

    'cant_assign_case' => [
        'title' => 'Why can\'t the case be assigned? Please choose!',
        'not_available_1' => 'On the following',
        'not_available_2' => 'calendar days, I am unable to take cases.',
        'professional' => 'Due to professional reasons.',
        'ethical' => 'Due to ethical reasons.',
    ],

    'case_save_phone_number' => 'Save phone number',

    'case_no_phone_number' => 'No phone number',

    'case_field_required' => 'Fields are required!',

    'case_phone_number_only_number_error' => 'The phone number can only contain numbers!',

    'case_phone_number_length_error' => 'The phone number cannot be longer than 15 characters!',
];
