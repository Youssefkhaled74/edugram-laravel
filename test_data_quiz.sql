SET FOREIGN_KEY_CHECKS=0;

-- ============================================
-- 1. QUESTION GROUP
-- ============================================
INSERT INTO `question_groups` (`id`, `title`, `active_status`, `created_at`, `updated_at`, `user_id`, `lms_id`) VALUES
(1, 'Test Group 1', 1, NOW(), NOW(), 1, 1);

-- ============================================
-- 3. QUESTION BANK (Multiple Choice: type='M')
-- ============================================
INSERT INTO `question_banks` (`id`, `type`, `question`, `marks`, `number_of_option`, `q_group_id`, `category_id`, `active_status`, `created_at`, `updated_at`, `user_id`, `lms_id`, `level`) VALUES
(1, 'M', '{"ar":"ما عاصمة مصر؟","en":"What is the capital of Egypt?"}', 5, '4', 1, 1, 1, NOW(), NOW(), 1, 1, 1),
(2, 'M', '{"ar":"كم عدد الألوان في قوس قزح؟","en":"How many colors are in a rainbow?"}', 5, '4', 1, 1, 1, NOW(), NOW(), 1, 1, 1),
(3, 'M', '{"ar":"من اخترع المصباح الكهربائي؟","en":"Who invented the light bulb?"}', 5, '4', 1, 1, 1, NOW(), NOW(), 1, 1, 1),
(4, 'M', '{"ar":"كم عدد أيام الأسبوع؟","en":"How many days are in a week?"}', 5, '4', 1, 1, 1, NOW(), NOW(), 1, 1, 1),
(5, 'M', '{"ar":"ما أكبر قارة في العالم؟","en":"What is the largest continent?"}', 5, '4', 1, 1, 1, NOW(), NOW(), 1, 1, 1);

-- ============================================
-- 4. QUESTION OPTIONS (status=1 for correct)
-- ============================================
INSERT INTO `question_bank_mu_options` (`id`, `question_bank_id`, `title`, `status`, `active_status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `lms_id`, `position`) VALUES
-- Q1: Cairo
(1, 1, '{"ar":"القاهرة","en":"Cairo"}', 1, 1, NOW(), NOW(), 1, 1, 1, 0),
(2, 1, '{"ar":"الإسكندرية","en":"Alexandria"}', 0, 1, NOW(), NOW(), 1, 1, 1, 1),
(3, 1, '{"ar":"الجيزة","en":"Giza"}', 0, 1, NOW(), NOW(), 1, 1, 1, 2),
(4, 1, '{"ar":"أسوان","en":"Aswan"}', 0, 1, NOW(), NOW(), 1, 1, 1, 3),
-- Q2: 7
(5, 2, '{"ar":"5","en":"5"}', 0, 1, NOW(), NOW(), 1, 1, 1, 0),
(6, 2, '{"ar":"6","en":"6"}', 0, 1, NOW(), NOW(), 1, 1, 1, 1),
(7, 2, '{"ar":"7","en":"7"}', 1, 1, NOW(), NOW(), 1, 1, 1, 2),
(8, 2, '{"ar":"8","en":"8"}', 0, 1, NOW(), NOW(), 1, 1, 1, 3),
-- Q3: Edison
(9, 3, '{"ar":"نيوتن","en":"Newton"}', 0, 1, NOW(), NOW(), 1, 1, 1, 0),
(10, 3, '{"ar":"إديسون","en":"Edison"}', 1, 1, NOW(), NOW(), 1, 1, 1, 1),
(11, 3, '{"ar":"أينشتاين","en":"Einstein"}', 0, 1, NOW(), NOW(), 1, 1, 1, 2),
(12, 3, '{"ar":"فاراداي","en":"Faraday"}', 0, 1, NOW(), NOW(), 1, 1, 1, 3),
-- Q4: 7
(13, 4, '{"ar":"5","en":"5"}', 0, 1, NOW(), NOW(), 1, 1, 1, 0),
(14, 4, '{"ar":"6","en":"6"}', 0, 1, NOW(), NOW(), 1, 1, 1, 1),
(15, 4, '{"ar":"7","en":"7"}', 1, 1, NOW(), NOW(), 1, 1, 1, 2),
(16, 4, '{"ar":"8","en":"8"}', 0, 1, NOW(), NOW(), 1, 1, 1, 3),
-- Q5: Asia
(17, 5, '{"ar":"إفريقيا","en":"Africa"}', 0, 1, NOW(), NOW(), 1, 1, 1, 0),
(18, 5, '{"ar":"أوروبا","en":"Europe"}', 0, 1, NOW(), NOW(), 1, 1, 1, 1),
(19, 5, '{"ar":"آسيا","en":"Asia"}', 1, 1, NOW(), NOW(), 1, 1, 1, 2),
(20, 5, '{"ar":"أمريكا الشمالية","en":"North America"}', 0, 1, NOW(), NOW(), 1, 1, 1, 3);

-- ============================================
-- 5. ONLINE QUIZ (translatable: title, instruction)
-- ============================================
INSERT INTO `online_quizzes` (`id`, `title`, `percentage`, `instruction`, `status`, `active_status`, `created_at`, `updated_at`, `category_id`, `course_id`, `created_by`, `updated_by`, `random_question`, `question_time_type`, `question_time`, `question_review`, `show_result_each_submit`, `multiple_attend`, `lms_id`, `total_questions`, `total_marks`) VALUES
(1, '{"ar":"اختبار عام 1","en":"General Quiz 1"}', 50, '{"ar":"أجب عن جميع الأسئلة","en":"Answer all questions"}', 1, 1, NOW(), NOW(), 1, NULL, 1, 1, 0, 0, 2, 1, 1, 1, 1, 5, 25);

-- ============================================
-- 6. ASSIGN QUESTIONS TO QUIZ
-- ============================================
INSERT INTO `online_exam_question_assigns` (`online_exam_id`, `question_bank_id`, `created_at`, `updated_at`, `created_by`, `updated_by`, `lms_id`) VALUES
(1, 1, NOW(), NOW(), 1, 1, 1),
(1, 2, NOW(), NOW(), 1, 1, 1),
(1, 3, NOW(), NOW(), 1, 1, 1),
(1, 4, NOW(), NOW(), 1, 1, 1),
(1, 5, NOW(), NOW(), 1, 1, 1);

-- ============================================
-- 7. COURSE (translatable: title, about, requirements, outcomes)
-- ============================================
INSERT INTO `courses` (`id`, `category_id`, `user_id`, `lang_id`, `title`, `slug`, `duration`, `image`, `price`, `discount_price`, `publish`, `status`, `level`, `host`, `about`, `total_enrolled`, `type`, `created_at`, `updated_at`, `requirements`, `outcomes`, `total_chapters`, `total_lessons`, `total_quiz_lessons`) VALUES
(1, 1, 1, 19, '{"ar":"كورس اختبار تجريبي","en":"Test Course for Quiz"}', 'test-course-for-quiz', '10', 'public/frontend/infixlmstheme/img/course/1.jpg', 100.00, 50.00, 1, 1, 1, 'Self', '{"ar":"هذا كورس تجريبي لاختبار عرض الكويزات في المنهج","en":"This is a test course to verify quiz display in the curriculum"}', 0, 1, NOW(), NOW(), '{"ar":"لا توجد متطلبات","en":"No requirements"}', '{"ar":"فهم كيفية عمل الكويزات","en":"Understand how quizzes work"}', 2, 4, 1);

-- ============================================
-- 8. CHAPTERS (plain string name)
-- ============================================
INSERT INTO `chapters` (`id`, `course_id`, `name`, `chapter_no`, `is_lock`, `created_at`, `updated_at`, `position`, `lms_id`) VALUES
(1, 1, 'Chapter 1: Introduction', 1, 0, NOW(), NOW(), 1, 1),
(2, 1, 'Chapter 2: Quizzes & Review', 2, 0, NOW(), NOW(), 2, 1);

-- ============================================
-- 9. LESSONS (plain string name & description)
-- ============================================
INSERT INTO `lessons` (`id`, `course_id`, `chapter_id`, `quiz_id`, `name`, `description`, `video_url`, `host`, `duration`, `is_lock`, `is_quiz`, `created_at`, `updated_at`, `position`, `lms_id`) VALUES
-- Chapter 1: Regular video lessons
(1, 1, 1, NULL, 'Lesson 1: Welcome', 'Welcome to the course', 'dummy.mp4', 'Self', '10', 0, 0, NOW(), NOW(), 1, 1),
(2, 1, 1, NULL, 'Lesson 2: Basic Concepts', 'Learn the basic concepts', 'dummy.mp4', 'Self', '15', 1, 0, NOW(), NOW(), 2, 1),
(3, 1, 1, NULL, 'Lesson 3: Practical Exercise', 'Hands-on practice', 'dummy.mp4', 'Self', '20', 0, 0, NOW(), NOW(), 3, 1),

-- Chapter 2: Quiz lesson + regular lesson
(4, 1, 2, 1, 'Chapter 1 Quiz', NULL, NULL, NULL, NULL, 0, 1, NOW(), NOW(), 1, 1),
(5, 1, 2, NULL, 'Lesson 4: Review & Summary', 'Quick review of all topics', 'dummy.mp4', 'Self', '10', 0, 0, NOW(), NOW(), 2, 1);

SET FOREIGN_KEY_CHECKS=1;
