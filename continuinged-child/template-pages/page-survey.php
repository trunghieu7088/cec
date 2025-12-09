<?php
/**
 * Template Name: Page Survey
 * Template Post Type: page
 * Description: Survey Page
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Generate random CAPTCHA code
session_start();
$captcha_code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
$_SESSION['survey_captcha'] = $captcha_code;

get_header();
?> 
<style>
    .content-card {
        background: white;
        border-radius: 12px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .section-title {
        color: var(--primary-blue);
        font-size: 1.8rem;
        margin-bottom: 25px;
        font-weight: 600;
        padding-bottom: 15px;
        border-bottom: 3px solid var(--primary-blue);
    }

    .survey-intro {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #444;
        margin-bottom: 30px;
    }

    .question-block {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 25px;
        border-left: 4px solid var(--primary-blue);
    }

    .question-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .radio-group, .checkbox-group {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .checkbox-group .form-check:last-child {
  /* Đảm bảo nó chiếm toàn bộ chiều rộng, buộc các mục khác phải xuống hàng */
    width: 100%; 
    }
    .form-check {
        margin-bottom: 10px;
    }

    .form-check-input:checked {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
    }

    .form-check-label {
        font-size: 1rem;
        color: #555;
        cursor: pointer;
    }

    .form-control, .form-select {
        border: 1px solid #ddd;
        padding: 12px;
        border-radius: 8px;
        font-size: 1rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.25rem rgba(51, 102, 102, 0.15);
    }

    .btn-submit {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
        font-weight: 600;
        padding: 12px 40px;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 1.1rem;
        width:250px;
    }

    .btn-submit:hover {
        background-color: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(51, 102, 102, 0.2);
        color: white;
    }

    .btn-submit:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    .note-box {
        background: #fff9e6;
        border-left: 4px solid #ffcc00;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
        font-size: 1.05rem;
        color: #664d00;
    }

    .captcha-box {
        background: #e8f5e9;
        border: 2px solid #4caf50;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        margin: 30px 0;
    }

    .captcha-text {
        background: white;
        padding: 10px 20px;
        border: 2px solid #333;
        display: inline-block;
        font-family: 'Courier New', monospace;
        font-size: 1.5rem;
        font-weight: bold;
        letter-spacing: 5px;
        margin: 10px 0;
        border-radius: 4px;
        user-select: none;
    }

    .success-message {
        background: #d4edda;
        border: 2px solid #28a745;
        color: #155724;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
        display: none;
    }

    .success-message h3 {
        margin-top: 0;
        color: #155724;
    }

    .discount-code {
        background: #fff;
        padding: 15px;
        border: 2px dashed #28a745;
        border-radius: 5px;
        font-size: 1.5rem;
        font-weight: bold;
        color: #28a745;
        margin: 15px 0;
        letter-spacing: 3px;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.9rem;
        margin-top: 5px;
        display: none;
    }

    .field-error {
        border-color: #dc3545 !important;
    }

    @media (max-width: 768px) {
        .content-card {
            padding: 25px;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .radio-group {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>

<div class="container py-5">
    <div class="content-card">
        <h2 class="section-title">Survey</h2>
        
        <p class="survey-intro">
            We value your comments. We will take your comments into consideration as we try to continually improve our website and the quantity and quality of the courses that we provide. Feel free to call or email us with additional comments or suggestions.
        </p>

        <hr class="my-4">

        <form id="wp-survey-form" class="survey-form" data-type="general">
            <input type="hidden" name="_survery_nonce_field" id="_survery_nonce_field" value="<?php echo wp_create_nonce('general_survey_nonce'); ?>">
            <input type="hidden" name="action" value="submit_survey">
            <input type="hidden" name="survey_type" value="general">
            <input type="hidden" name="course_id" value="0">
            
            <!-- All your existing question blocks here -->
                         <!-- Question 1 -->
                <div class="question-block">
                    <div class="question-title">How many Home Study courses have you taken in the past year?</div>
                    <div class="radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r10" value="0" id="r10_0">
                            <label class="form-check-label" for="r10_0">0</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r10" value="1" id="r10_1">
                            <label class="form-check-label" for="r10_1">1</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r10" value="2" id="r10_2">
                            <label class="form-check-label" for="r10_2">2</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r10" value="3" id="r10_3">
                            <label class="form-check-label" for="r10_3">3-4</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r10" value="4" id="r10_4">
                            <label class="form-check-label" for="r10_4">5+</label>
                        </div>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="question-block">
                    <div class="question-title">How many of these courses were taken entirely on the Internet?</div>
                    <div class="radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r20" value="0" id="r20_0">
                            <label class="form-check-label" for="r20_0">0</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r20" value="1" id="r20_1">
                            <label class="form-check-label" for="r20_1">1</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r20" value="2" id="r20_2">
                            <label class="form-check-label" for="r20_2">2</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r20" value="3" id="r20_3">
                            <label class="form-check-label" for="r20_3">3-4</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r20" value="4" id="r20_4">
                            <label class="form-check-label" for="r20_4">5+</label>
                        </div>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="question-block">
                    <div class="question-title">How many Home Study courses do you plan to take in the next year?</div>
                    <div class="radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r30" value="0" id="r30_0">
                            <label class="form-check-label" for="r30_0">0</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r30" value="1" id="r30_1">
                            <label class="form-check-label" for="r30_1">1</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r30" value="2" id="r30_2">
                            <label class="form-check-label" for="r30_2">2</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r30" value="3" id="r30_3">
                            <label class="form-check-label" for="r30_3">3-4</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r30" value="4" id="r30_4">
                            <label class="form-check-label" for="r30_4">5+</label>
                        </div>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="question-block">
                    <div class="question-title">How many of these courses do you expect to take entirely on the Internet?</div>
                    <div class="radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r40" value="0" id="r40_0">
                            <label class="form-check-label" for="r40_0">0</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r40" value="1" id="r40_1">
                            <label class="form-check-label" for="r40_1">1</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r40" value="2" id="r40_2">
                            <label class="form-check-label" for="r40_2">2</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r40" value="3" id="r40_3">
                            <label class="form-check-label" for="r40_3">3-4</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r40" value="4" id="r40_4">
                            <label class="form-check-label" for="r40_4">5+</label>
                        </div>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="question-block">
                    <div class="question-title">What lengths do you prefer for an Internet course?</div>
                    <div class="checkbox-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r41" value="1" id="r41">
                            <label class="form-check-label" for="r41">1 hour</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r42" value="1" id="r42">
                            <label class="form-check-label" for="r42">2 hours</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r43" value="1" id="r43">
                            <label class="form-check-label" for="r43">3 hours</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r44" value="1" id="r44">
                            <label class="form-check-label" for="r44">4 hours</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r45" value="1" id="r45">
                            <label class="form-check-label" for="r45">5+ hours</label>
                        </div>
                    </div>
                </div>

                <!-- Question 6 -->
                <div class="question-block">
                    <div class="question-title">Do our course prices seem:</div>
                    <div class="radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r50" value="1" id="r50_1">
                            <label class="form-check-label" for="r50_1">Too high</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r50" value="2" id="r50_2">
                            <label class="form-check-label" for="r50_2">A little high</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r50" value="3" id="r50_3">
                            <label class="form-check-label" for="r50_3">Reasonable</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r50" value="4" id="r50_4">
                            <label class="form-check-label" for="r50_4">A little low</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r50" value="5" id="r50_5">
                            <label class="form-check-label" for="r50_5">A bargain</label>
                        </div>
                    </div>
                </div>

                <!-- Question 7 -->
                <div class="question-block">
                    <div class="question-title">How did you find out about our website?</div>
                    <div class="mb-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="r60" value="1" id="r60_1">
                            <label class="form-check-label" for="r60_1">From a friend/colleague</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="r60" value="2" id="r60_2">
                            <label class="form-check-label" for="r60_2">Mailed advertisement</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="r60" value="3" id="r60_3">
                            <label class="form-check-label" for="r60_3">Internet link/search engine</label>
                            <input type="text" class="form-control mt-2" name="r60v3text" placeholder="Please specify">
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="r60" value="4" id="r60_4">
                            <label class="form-check-label" for="r60_4">Local newsletter</label>
                            <input type="text" class="form-control mt-2" name="r60v4text" placeholder="Please specify">
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="r60" value="5" id="r60_5">
                            <label class="form-check-label" for="r60_5">Magazine</label>
                            <input type="text" class="form-control mt-2" name="r60v5text" placeholder="Please specify">
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="r60" value="6" id="r60_6">
                            <label class="form-check-label" for="r60_6">Article</label>
                            <input type="text" class="form-control mt-2" name="r60v6text" placeholder="Please specify">
                        </div>                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="r60" value="7" id="r60_7">
                            <label class="form-check-label" for="r60_7">Other</label>
                            <input type="text" class="form-control mt-2" name="r60v7text" placeholder="Please specify">
                        </div>
                    </div>
                </div>

                <!-- Question 8 -->
                <div class="question-block">
                    <div class="question-title">What is your current professional status?</div>
                    <div class="checkbox-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r70" value="1" id="r70">
                            <label class="form-check-label" for="r70">Psychologist</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r71" value="1" id="r71">
                            <label class="form-check-label" for="r71">Social Worker</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r72" value="1" id="r72">
                            <label class="form-check-label" for="r72">Marriage and Family Therapist</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r74" value="1" id="r74">
                            <label class="form-check-label" for="r74">Counselor</label>
                        </div>                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r73" value="1" id="r73">                            
                            <label class="form-check-label" for="r73">Other</label>
                            <input class="form-control mt-2" type="text"  name="r73text" placeholder="Please specify">
                        </div>
                    </div>
                </div>

                <!-- Question 9 - Topics -->
                <div class="question-block">
                    <div class="question-title">What topics would you like to study in an online course?</div>
                    <div class="row">
                        <!-- Cột trái -->
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r81" value="1" id="r81">
                                <label class="form-check-label" for="r81">ADHD</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r82" value="1" id="r82">
                                <label class="form-check-label" for="r82">Addiction</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r83" value="1" id="r83">
                                <label class="form-check-label" for="r83">Aging and Long Term Care</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r84" value="1" id="r84">
                                <label class="form-check-label" for="r84">Asperger's</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r85" value="1" id="r85">
                                <label class="form-check-label" for="r85">Behavioral Assessment</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r86" value="1" id="r86">
                                <label class="form-check-label" for="r86">Biofeedback</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r87" value="1" id="r87">
                                <label class="form-check-label" for="r87">Brief Psychotherapy</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r88" value="1" id="r88">
                                <label class="form-check-label" for="r88">Couples Therapy</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r89" value="1" id="r89">
                                <label class="form-check-label" for="r89">Crisis Intervention</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r90" value="1" id="r90">
                                <label class="form-check-label" for="r90">Death and Dying</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r91" value="1" id="r91">
                                <label class="form-check-label" for="r91">Depression</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r92" value="1" id="r92">
                                <label class="form-check-label" for="r92">Diagnosis/DSM-IV</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r93" value="1" id="r93">
                                <label class="form-check-label" for="r93">Difficult Clients</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r94" value="1" id="r94">
                                <label class="form-check-label" for="r94">Divorce</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r95" value="1" id="r95">
                                <label class="form-check-label" for="r95">Domestic Violence</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r96" value="1" id="r96">
                                <label class="form-check-label" for="r96">Drug Abuse</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r97" value="1" id="r97">
                                <label class="form-check-label" for="r97">Eating Disorders</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r98" value="1" id="r98">
                                <label class="form-check-label" for="r98">Ethics</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r99" value="1" id="r99">
                                <label class="form-check-label" for="r99">Family Therapy</label>
                            </div>
                        </div>

                        <!-- Cột phải -->
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r100" value="1" id="r100">
                                <label class="form-check-label" for="r100">Forensic Psychology</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r101" value="1" id="r101">
                                <label class="form-check-label" for="r101">Gay/Lesbian Issues</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r102" value="1" id="r102">
                                <label class="form-check-label" for="r102">Group Therapy</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r103" value="1" id="r103">
                                <label class="form-check-label" for="r103">Health Psychology</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r104" value="1" id="r104">
                                <label class="form-check-label" for="r104">Hypnosis</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r105" value="1" id="r105">
                                <label class="form-check-label" for="r105">Neuropsychology</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r106" value="1" id="r106">
                                <label class="form-check-label" for="r106">Organic Mental Disorders</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r107" value="1" id="r107">
                                <label class="form-check-label" for="r107">Pain Management</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r108" value="1" id="r108">
                                <label class="form-check-label" for="r108">Parenting Skills</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r109" value="1" id="r109">
                                <label class="form-check-label" for="r109">Personality Disorders</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r110" value="1" id="r110">
                                <label class="form-check-label" for="r110">Play Therapy</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r111" value="1" id="r111">
                                <label class="form-check-label" for="r111">Post-Traumatic Stress Disorder</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r112" value="1" id="r112">
                                <label class="form-check-label" for="r112">Professional Burnout</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r113" value="1" id="r113">
                                <label class="form-check-label" for="r113">Psychopharmacology</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r114" value="1" id="r114">
                                <label class="form-check-label" for="r114">Schizophrenia</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r115" value="1" id="r115">
                                <label class="form-check-label" for="r115">Social Skills Training</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r116" value="1" id="r116">
                                <label class="form-check-label" for="r116">Supervision</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="r117" value="1" id="r117">
                                <label class="form-check-label" for="r117">Other (please list below)</label>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- ... (keep all existing questions) ... -->

            <!-- Contact Information -->
            <div class="question-block">
                <div class="question-title">Contact Information (Optional)</div>
                
                <div class="row mb-3">
                    <label for="r160" class="col-sm-3 col-form-label">Name:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="r160" id="r160" placeholder="Your name">
                        <div class="error-message" id="error-r160">Please enter your name</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="r170" class="col-sm-3 col-form-label">Email:</label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" name="r170" id="r170" placeholder="your.email@example.com">
                        <div class="error-message" id="error-r170">Please enter a valid email</div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="r180" value="1" id="r180">
                            <label class="form-check-label" for="r180">
                                Notify me when new courses are available
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="r190" class="col-sm-3 col-form-label">Phone:</label>
                    <div class="col-sm-9">
                        <input type="tel" class="form-control" name="r190" id="r190" placeholder="Your phone number">
                        <div class="error-message" id="error-r190">Please enter your phone number</div>
                    </div>
                </div>
            </div>

            <!-- Human Verification -->
            <div class="captcha-box">
                <p class="mb-2"><strong>Human Verification</strong></p>
                <p>Please type the following text:</p>
                <div class="captcha-text"><?php echo $captcha_code; ?></div>
                <input type="text" class="form-control d-inline-block" name="HumanVerify" id="HumanVerify" style="width: 200px; margin-top: 10px;" placeholder="Enter text here" required>
                <div class="error-message" id="error-captcha" style="display: block; margin-top: 10px;">Please enter the verification code</div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" name="SubmitSurvey" class="btn btn-submit" id="submitBtn">
                    <i class="bi bi-send me-2"></i>Submit Survey
                </button>
            </div>

            <div class="note-box mt-4">
                <strong>Note:</strong> All information provided will be kept confidential and used only to improve our services. Thank you for taking the time to complete this survey!
            </div>

            <!-- Success Message (Hidden by default) -->
            <div class="success-message" id="successMessage">
                <h3>✓ Thank You!</h3>
                <p>Your survey has been submitted successfully!</p>
                <p>As a token of our appreciation, here's your discount code:</p>
                <div class="discount-code" id="discountCode"></div>
                <p><small>Use this code for 10% off your next course purchase.</small></p>
            </div>
        </form>
    </div>
</div>

<?php get_footer(); ?>

<script>
jQuery(document).ready(function($) {
    let isSubmitting = false;

    // Validate form before submit
    function validateForm() {
        let isValid = true;
        let firstError = null;

        // Clear all previous errors
        $('.error-message').hide();
        $('.form-control').removeClass('field-error');

        // Validate Name
        if ($('#r160').val().trim() === '') {
            $('#error-r160').show();
            $('#r160').addClass('field-error');
            isValid = false;
            if (!firstError) firstError = $('#r160');
        }

        // Validate Email
        const email = $('#r170').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email === '' || !emailRegex.test(email)) {
            $('#error-r170').show();
            $('#r170').addClass('field-error');
            isValid = false;
            if (!firstError) firstError = $('#r170');
        }

        // Validate Phone
        if ($('#r190').val().trim() === '') {
            $('#error-r190').show();
            $('#r190').addClass('field-error');
            isValid = false;
            if (!firstError) firstError = $('#r190');
        }

        // Validate CAPTCHA
        if ($('#HumanVerify').val().trim() === '') {
            $('#error-captcha').text('Please enter the verification code').show();
            $('#HumanVerify').addClass('field-error');
            isValid = false;
            if (!firstError) firstError = $('#HumanVerify');
        }

        // Check if at least one question is answered
        const hasAnswer = $('input[type="radio"]:checked, input[type="checkbox"]:checked, textarea').filter(function() {
            return $(this).val() !== '' && $(this).val() !== '0';
        }).length > 0;

        if (!hasAnswer) {
            alert('Please answer at least one survey question before submitting.');
            isValid = false;
        }

        // Scroll to first error
        if (!isValid && firstError) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }

        return isValid;
    }

    // Form submission
    $('#wp-survey-form').on('submit', function(e) {
        e.preventDefault();
        
        if (isSubmitting) {
            return false;
        }

        if (!validateForm()) {
            return false;
        }

        isSubmitting = true;
        $('#submitBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Submitting...');

        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $('#discountCode').text(response.data.discount_code);
                    $('#successMessage').slideDown();
                    
                    // Scroll to success message
                    $('html, body').animate({
                        scrollTop: $('#successMessage').offset().top - 100
                    }, 500);
                    
                    // Reset form
                    $('#wp-survey-form')[0].reset();
                    
                    // Hide submit button
                    $('#submitBtn').hide();
                } else {
                    alert('Error: ' + response.data);
                    $('#submitBtn').prop('disabled', false).html('<i class="bi bi-send me-2"></i>Submit Survey');
                }
                isSubmitting = false;
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $('#submitBtn').prop('disabled', false).html('<i class="bi bi-send me-2"></i>Submit Survey');
                isSubmitting = false;
            }
        });
    });

    // Real-time validation
    $('#r160, #r170, #r190, #HumanVerify').on('blur', function() {
        const field = $(this);
        const errorDiv = $('#error-' + field.attr('id').replace('r', 'r'));
        
        if (field.val().trim() === '') {
            field.addClass('field-error');
            errorDiv.show();
        } else {
            field.removeClass('field-error');
            errorDiv.hide();
        }
    });

    // Email validation on blur
    $('#r170').on('blur', function() {
        const email = $(this).val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email !== '' && !emailRegex.test(email)) {
            $(this).addClass('field-error');
            $('#error-r170').text('Please enter a valid email address').show();
        }
    });
});
</script>