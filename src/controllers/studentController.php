<?php

namespace Tinhl\Bai01QuanlySv\Controllers;

use Tinhl\Bai01QuanlySv\Core\FlashMessage;
use Tinhl\Bai01QuanlySv\models\StudentModel;

class StudentController
{
    private $studentModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
    }

    public function index()
    {
        $keyword = $_GET['keyword'] ?? null;
        $students = $this->studentModel->getAllStudents($keyword);
        $editingStudent = null;

        require_once __DIR__ . '/../../views/studentList.php';
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';

            if (!empty($name) && !empty($email) && !empty($phone)) {
                $uploadResult = $this->handleAvatarUpload($_FILES['avatar'] ?? null);

                if (!$uploadResult['success']) {
                    FlashMessage::set('student_action', $uploadResult['message'], 'error');
                    header('Location: index.php');
                    exit();
                }

                $isAdded = $this->studentModel->addStudent(
                    $name,
                    $email,
                    $phone,
                    $uploadResult['filename']
                );

                if ($isAdded) {
                    FlashMessage::set('student_action', 'Thêm sinh viên thành công!', 'success');
                } else {
                    $this->deleteAvatarFile($uploadResult['filename']);
                    FlashMessage::set('student_action', 'Thêm sinh viên thất bại!', 'error');
                }
            } else {
                FlashMessage::set('student_action', 'Vui lòng nhập đầy đủ thông tin sinh viên.', 'error');
            }
        }

        header('Location: index.php');
        exit();
    }

    public function edit()
    {
        $studentId = (int) ($_GET['id'] ?? 0);

        if ($studentId <= 0) {
            FlashMessage::set('student_action', 'Không tìm thấy sinh viên cần sửa.', 'error');
            header('Location: index.php');
            exit();
        }

        $editingStudent = $this->studentModel->getStudentById($studentId);

        if (!$editingStudent) {
            FlashMessage::set('student_action', 'Sinh viên không tồn tại.', 'error');
            header('Location: index.php');
            exit();
        }

        $keyword = $_GET['keyword'] ?? null;
        $students = $this->studentModel->getAllStudents($keyword);

        require_once __DIR__ . '/../../views/studentList.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit();
        }

        $studentId = (int) ($_POST['id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';

        if ($studentId <= 0) {
            FlashMessage::set('student_action', 'Không tìm thấy sinh viên cần cập nhật.', 'error');
            header('Location: index.php');
            exit();
        }

        $currentStudent = $this->studentModel->getStudentById($studentId);

        if (!$currentStudent) {
            FlashMessage::set('student_action', 'Sinh viên không tồn tại.', 'error');
            header('Location: index.php');
            exit();
        }

        if (empty($name) || empty($email) || empty($phone)) {
            FlashMessage::set('student_action', 'Vui lòng nhập đầy đủ thông tin sinh viên.', 'error');
            header('Location: index.php?action=edit&id=' . $studentId);
            exit();
        }

        $uploadResult = $this->handleAvatarUpload($_FILES['avatar'] ?? null);

        if (!$uploadResult['success']) {
            FlashMessage::set('student_action', $uploadResult['message'], 'error');
            header('Location: index.php?action=edit&id=' . $studentId);
            exit();
        }

        $avatarFile = $currentStudent['avatar'] ?? null;
        $oldAvatarFile = $avatarFile;

        if (!empty($uploadResult['filename'])) {
            $avatarFile = $uploadResult['filename'];
        }

        $isUpdated = $this->studentModel->updateStudent(
            $studentId,
            $name,
            $email,
            $phone,
            $avatarFile
        );

        if ($isUpdated) {
            if (!empty($uploadResult['filename']) && !empty($oldAvatarFile) && $oldAvatarFile !== $avatarFile) {
                $this->deleteAvatarFile($oldAvatarFile);
            }

            FlashMessage::set('student_action', 'Cập nhật sinh viên thành công!', 'success');
            header('Location: index.php');
            exit();
        }

        if (!empty($uploadResult['filename'])) {
            $this->deleteAvatarFile($uploadResult['filename']);
        }

        FlashMessage::set('student_action', 'Cập nhật sinh viên thất bại!', 'error');
        header('Location: index.php?action=edit&id=' . $studentId);
        exit();
    }

    public function dashboard()
    {
        $stats = $this->studentModel->getStatistics();

        require_once __DIR__ . '/../../views/dashboard.php';
    }

    private function handleAvatarUpload($file)
    {
        if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => true, 'filename' => null, 'message' => ''];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'filename' => null, 'message' => 'Tải ảnh đại diện thất bại.'];
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            return ['success' => false, 'filename' => null, 'message' => 'Ảnh đại diện không được vượt quá 2MB.'];
        }

        $tmpName = $file['tmp_name'] ?? '';
        $imageInfo = @getimagesize($tmpName);
        $allowedTypes = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_GIF => 'gif',
            IMAGETYPE_WEBP => 'webp',
        ];

        if ($imageInfo === false || !isset($allowedTypes[$imageInfo[2]])) {
            return ['success' => false, 'filename' => null, 'message' => 'Ảnh đại diện phải là file JPG, PNG, GIF hoặc WEBP.'];
        }

        $uploadDir = $this->getAvatarUploadDirectory();

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            return ['success' => false, 'filename' => null, 'message' => 'Không thể tạo thư mục lưu ảnh đại diện.'];
        }

        $fileName = uniqid('avatar_', true) . '.' . $allowedTypes[$imageInfo[2]];
        $destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            return ['success' => false, 'filename' => null, 'message' => 'Không thể lưu file ảnh đại diện.'];
        }

        return ['success' => true, 'filename' => $fileName, 'message' => ''];
    }

    private function deleteAvatarFile($fileName)
    {
        if (empty($fileName)) {
            return;
        }

        $avatarPath = $this->getAvatarUploadDirectory() . DIRECTORY_SEPARATOR . $fileName;

        if (is_file($avatarPath)) {
            unlink($avatarPath);
        }
    }

    private function getAvatarUploadDirectory()
    {
        if (defined('PROJECT_ROOT')) {
            return PROJECT_ROOT . '/public/uploads/avatars';
        }

        return dirname(__DIR__, 2) . '/public/uploads/avatars';
    }
}
