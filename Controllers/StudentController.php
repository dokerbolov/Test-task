<?php

namespace Controllers;

use Config\Database;

class StudentController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param int $studentId
     * @return array
     * @throws \Exception
     */
    public function viewGrades($studentId)
    {
        $sql = "SELECT courses.name, grades.grade, grades.graded_at 
                FROM grades
                JOIN courses ON grades.course_id = courses.id
                WHERE grades.student_id = $studentId
                ORDER BY courses.name ASC";

        $grades = $this->db->execSql($sql);

        $result = [];
        foreach ($grades as $item) {
            $name = $item['name'];
            if (!isset($result[$name])) {
                $result[$name] = [
                    'name' => $name,
                    'grades' => []
                ];
            }
            $result[$name]['grades'][] = [
                'grade' => $item['grade'],
                'graded_at' => $item['graded_at']
            ];
        }
        $result = array_values($result);

        return $result ?: false;
    }

    /**
     * @param int $studentId
     * @param int $courseId
     * @throws \Exception
     */
    public function viewAverageGrade($studentId, $courseId)
    {
        $sql = "SELECT courses.name, COALESCE(ROUND(AVG(grades.grade), 2), 0) as average_grade
                FROM grades
                JOIN courses ON grades.course_id = courses.id
                WHERE grades.student_id = $studentId AND grades.course_id = $courseId
                GROUP BY courses.name;";

        $result = $this->db->execSql($sql);

        return $result ? $result : false;
    }
}
