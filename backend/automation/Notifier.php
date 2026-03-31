<?php
class Notifier {
    private $n8nBase = 'http://127.0.0.1:5678/webhook';

    public function projectSubmitted($studentName, $projectTitle, $teacherEmail, $teacherId) {
        $data = [
            'student_name'  => $studentName,
            'project_title' => $projectTitle,
            'teacher_email' => $teacherEmail,
            'teacher_id'    => $teacherId,
            'submitted_at'  => date('Y-m-d H:i:s')
        ];

        $ch = curl_init($this->n8nBase . '/project-submitted');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}
?>