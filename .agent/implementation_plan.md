# Implementation Plan: Attendance (Face Rec) & LMS (Exams/Quizzes)

## Overview
The user wants to implement two major modules:
1. **Attendance System with Face Recognition**: Students check in using facial recognition.
2. **LMS / Exam System**: Support for Daily Quizzes, Mid-terms (UTS), and Finals (UAS) with Multiple Choice and Essay questions.

## Phase 1: Database Schema & Models
- [ ] **Migration: Absensi (Attendance)**
    - `attendances`: `user_id`, `schedule_id` (nullable), `date`, `check_in_time`, `status`, `photo_path`.
    - Update `users`: Add `face_descriptor` (text/json) to store biometric data.
- [ ] **Migration: Exams (Ujian/Kuis)**
    - `exams`: `subject_id`, `created_by` (teacher_id), `classroom_id` (nullable, if specific to a class), `title`, `type` (quiz, uts, uas), `start_time`, `end_time`, `duration_minutes`.
    - `questions`: `exam_id`, `question_text`, `question_type` (multiple_choice, essay), `points`.
    - `question_options`: `question_id`, `option_text`, `is_correct`.
    - `exam_attempts`: `user_id`, `exam_id`, `start_time`, `submit_time`, `score`, `status` (in_progress, submitted).
    - `exam_answers`: `exam_attempt_id`, `question_id`, `answer_text`, `selected_option_id`.
- [ ] **Models**: Create models for all above tables and define relationships.

## Phase 2: Face Recognition Registration (Profile)
- [ ] **Route/View**: User Profile -> "Register Face Data".
- [ ] **Frontend**: Use `face-api.js` to capture face descriptors.
- [ ] **Backend**: Save JSON descriptor to `users` table.

## Phase 3: Attendance Feature
- [ ] **Attendance Page**: Simple page with Camera stream.
- [ ] **Logic**: 
    - Load user's face descriptor.
    - Detect face in video.
    - Match.
    - If match: POST to `/attendance/check-in`.

## Phase 4: Exam Management (Teacher)
- [ ] **CRUD Exams**: Teachers can create Exams/Quizzes for their Subjects.
- [ ] **Question Builder**: Interface to add questions and options.

## Phase 5: Student Exam Interface
- [ ] **Exam List**: Students see available exams.
- [ ] **Taking Exam**: Timer based interface, input answers.
- [ ] **Grading**: Auto-grade multiple choice. Manual grade essay.

## Phase 6: Reporting
- [ ] **Attendance Report**
- [ ] **Grade Book**
