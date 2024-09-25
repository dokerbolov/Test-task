<?php

namespace Controllers;

use Config\Database;
use Helpers\Logger;
use Helpers\Validator;

class TeacherController
{
    private $db;
    private $validator;

    public function __construct($db)
    {
        $this->db = $db;
        $this->validator = new Validator(new Logger('validator.log'), $db);
    }

    /**
     * @param int $teacherId
     * @return array
     * @throws \Exception
     */
    public function getEnrolledStudents($teacherId)
    {
        $listOfCourses = "SELECT id,name FROM courses WHERE teacher_id = $teacherId";
        $courses = $this->db->execSql($listOfCourses);
        $result = [];
        foreach ($courses as $course) {
            $id = $course['id'];

            $sql = "SELECT students.id, students.first_name, students.last_name
            FROM students
            JOIN enrollments ON students.id = enrollments.student_id
            JOIN courses ON enrollments.course_id = courses.id
            WHERE courses.id = $id AND courses.teacher_id = $teacherId";

            $students = $this->db->execSql($sql);

            $courseList = [
                'name' => $course['name'],
                'students' => $students
            ];

            $result[] = $courseList;
        }
        return $result ? $result : false;
    }


    /**
     * @param int $studentId
     * @param int $courseId
     * @param int $grade
     * @param int $teacherId
     * @return bool
     * @throws \Exception
     */
    public function assignGrade($studentId, $courseId, $grade, $teacherId, $gradeId = null)
    {
        $query = "SELECT students.id  
                FROM students 
                JOIN enrollments ON students.id = enrollments.student_id
                JOIN courses ON enrollments.course_id = courses.id
                WHERE students.id = $studentId AND courses.teacher_id = $teacherId
                LIMIT 1";

        if(!$this->validator->isEmpty($query)){
            return false;
        }

        if($gradeId){
            $checkSql = "SELECT graded_at 
                     FROM grades 
                     WHERE id = $gradeId";
            $gradeInfo = $this->db->execSql($checkSql);

            if ($gradeInfo && !empty($gradeInfo[0]['graded_at'])) {
                $gradedAt = new \DateTime($gradeInfo[0]['graded_at']);
                $now = new \DateTime();
                $interval = $now->diff($gradedAt);

                if ($interval->days > 7) {
                    return false;
                }
            }

            $sql = "UPDATE grades SET grade = $grade, graded_at = NOW() 
                WHERE id = $gradeId";
        } else {
            $sql = "INSERT INTO grades (student_id, course_id, grade, graded_at) 
                VALUES ($studentId, $courseId, $grade, now())
                ON DUPLICATE KEY UPDATE grade = $grade";
        }

        $result = $this->db->execSql($sql);

        return $result !== false;
    }

    /**
     * @param int $courseId
     * @param int $teacherId
     * @return false
     * @throws \Exception
     */
    public function viewGradeHistory($courseId, $teacherId)
    {
        $query = "SELECT teachers.id  
                FROM teachers
                JOIN courses ON teachers.id = courses.teacher_id
                WHERE teachers.id = $teacherId AND courses.id = $courseId
                LIMIT 1";

        if(!$this->validator->isEmpty($query)){
            return false;
        }

        $sql = "SELECT students.first_name, students.last_name, grade, graded_at 
                FROM grades
                JOIN students ON grades.student_id = students.id
                WHERE course_id = $courseId
                ORDER BY graded_at DESC, students.last_name ASC";

        $result = $this->db->execSql($sql);

        return $result ? $result : false;
    }
}
