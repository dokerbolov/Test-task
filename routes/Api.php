<?php

namespace routes;

use Helpers\Responses;
use Helpers\Router;
use Controllers\TeacherController;
use Controllers\StudentController;
use Helpers\Validator;
use Helpers\Logger;

class Api
{
    private $router;
    private $teacherController;
    private $studentController;
    private $logger;
    private $response;

    public function __construct(TeacherController $teacherController, StudentController $studentController)
    {
        $this->router = new Router(new Logger('requests.log'));
        $this->teacherController = $teacherController;
        $this->studentController = $studentController;
        $this->logger = new Logger('validator.log');
        $this->response = new Responses();

        $this->defineRoutes();
    }

    private function defineRoutes()
    {
        // Teacher routes
        $this->router->get('/courses', [$this, 'getCourses']);
        $this->router->get('/teacher/students', [$this, 'getEnrolledStudents']);
        $this->router->post('/teacher/grade', [$this, 'assignGrade']);
        $this->router->get('/teacher/grade-history', [$this, 'viewGradeHistory']);

        // Student routes
        $this->router->get('/student/grades', [$this, 'viewGrades']);
        $this->router->get('/student/average-grades', [$this, 'viewAverageGrade']);

        $this->router->handleRequest();
    }


    /**
     * @throws \Exception
     */
    public function getEnrolledStudents()
    {
        $validator = new Validator($this->logger);
        $teacherId = $_GET['teacherId'];

        $validator->required($teacherId, 'teacherId');
        $validator->isNumeric($teacherId, 'teacherId');
        $validator->validate();

        $students = $this->teacherController->getEnrolledStudents($teacherId);
        if($students){
            $this->response->success(['message' => 'Students retrieved successfully', 'data' => $students]);
        } else {
            $this->response->error('No students found', 404);
        }
    }

    /**
     * @throws \Exception
     */
    public function assignGrade()
    {
        $validator = new Validator($this->logger);

        $studentId = $_POST['studentId'];
        $courseId = $_POST['courseId'];
        $grade = $_POST['grade'];
        $teacherId = $_POST['teacherId'];
        $gradeId = isset($_POST['gradeId']) ? $_POST['gradeId'] : null;

        $validator->required($studentId, 'studentId');
        $validator->isNumeric($studentId, 'studentId');

        $validator->required($courseId, 'courseId');
        $validator->isNumeric($courseId, 'courseId');

        $validator->required($grade, 'grade');
        $validator->isNumeric($grade, 'grade');
        $validator->isInRange($grade, 'grade', 0, 100);

        $validator->required($teacherId, 'teacherId');
        $validator->isNumeric($teacherId, 'teacherId');

        if ($gradeId !== null) {
            $validator->isNumeric($gradeId, 'gradeId');
        }

        $validator->validate();

        $response = $this->teacherController->assignGrade($studentId, $courseId, $grade, $teacherId, $gradeId);

        if ($response) {
            $this->response->success(['message' => 'Grade assigned successfully']);
        } else {
            $this->response->error('Grade not setted', 500);
        }
    }


    /**
     * @throws \Exception
     */
    public function viewGradeHistory()
    {
        $validator = new Validator($this->logger);
        $courseId = $_GET['courseId'];
        $teacherId = $_GET['teacherId'];

        $validator->required($courseId, 'courseId');
        $validator->isNumeric($courseId, 'courseId');

        $validator->required($teacherId, 'teacherId');
        $validator->isNumeric($teacherId, 'teacherId');
        $validator->validate();

        $history = $this->teacherController->viewGradeHistory($courseId, $teacherId);

        if($history){
            $this->response->success(['message' => 'Grade history retrieved successfully', 'data' => $history]);
        } else {
            $this->response->error('No history found', 404);
        }
    }

    /**
     * @throws \Exception
     */
    public function viewGrades()
    {
        $validator = new Validator($this->logger);
        $studentId = $_GET['studentId'];

        $validator->required($studentId, 'studentId');
        $validator->isNumeric($studentId, 'studentId');
        $validator->validate();

        $grades = $this->studentController->viewGrades($studentId);

        if($grades){
            $this->response->success(['message' => 'Grades retrieved successfully', 'data' => $grades]);
        } else {
            $this->response->error('No grades found', 404);
        }
    }

    /**
     * @throws \Exception
     */
    public function viewAverageGrade()
    {
        $validator = new Validator($this->logger);
        $studentId = $_GET['studentId'];
        $courseId = $_GET['courseId'];

        $validator->required($studentId, 'studentId');
        $validator->isNumeric($studentId, 'studentId');

        $validator->required($courseId, 'courseId');
        $validator->isNumeric($courseId, 'courseId');
        $validator->validate();

        $averageGrade = $this->studentController->viewAverageGrade($studentId, $courseId);

        if($averageGrade){
            $this->response->success(['message' => 'Average grade retrieved successfully', 'data' => $averageGrade]);
        } else {
            $this->response->error('No average grade found', 500);
        }
    }
}
