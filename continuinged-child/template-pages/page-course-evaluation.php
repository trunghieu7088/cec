<?php
/**
 * Template Name: Page Course Evaluation
 * Template Post Type: page
 * Description: Page Course Evaluation
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if(isset($_GET['certificate_id']))
{
    $pre_certificate=get_post($_GET['certificate_id']);
    if($pre_certificate && $pre_certificate->post_type=='llms_my_certificate')
    {
        $certificate_id=$pre_certificate->ID;
    }
    else
    {
        wp_redirect(site_url('home'));
    }
}
else
{
     wp_redirect(site_url('home'));
}
$certificate = new LLMS_User_Certificate($certificate_id);
//prevent other user from accessing the certificate page
if($certificate->post->post_author != get_current_user_id())
{
     wp_redirect(site_url('home'));
}
$user_id=$certificate->post->post_author;
$course_id=$certificate->get('related');
$course_id=$certificate->get('related');

if($course_id)
{
    $course_manager = my_lifterlms_courses();
    $course_data = $course_manager->get_single_course_data($course_id);
}
get_header();
?>
  <div class="container py-5">
        <div class="content-card">
            <h2 class="section-title">Course Evaluation</h2>
            <?php 
                if(check_survey_response_exists(get_current_user_id(),$course_id))
                    {
                $message = '
                    <div style="
                        border: 1px solid #4CAF50; 
                        background-color: #E8F5E9; 
                        color: #2E7D32; 
                        padding: 20px; 
                        margin: 20px 0; 
                        border-radius: 8px; 
                        font-family: Arial, sans-serif;
                        text-align: center;
                    ">
                        <h2 style="margin-top: 0; color: #1B5E20;">Congrats</h2>
                        <p style="font-size: 1.1em;">You have already put your evaluation</p>
                        <p>You can close this page or <a href="' . site_url('home') . '" style="color: #1B5E20; font-weight: bold;">return to homepage</a>.</p>
                    </div>
                ';      
                echo $message;       
                exit;
            }
            else
            {

            
            ?>
            <p class="survey-intro">
                Before printing your certificate, please complete this course evaluation. We value your feedback and will use this information to improve our website and courses. We will also provide this information to the course author.
            </p>

            <div class="note-box" style="background: #e3f2fd; border-left-color: #2196f3;">
                <p class="mb-2"><strong>Course:</strong> <?php echo $course_data['post_title']; ?></p>
                <p class="mb-0"><strong>Author:</strong>
             <?php
                $instructor_list=$course_data['instructors'];
                        if($instructor_list)                            
                        {   
                            $total_instructors=count($instructor_list);
                            $count=0;                            
                            foreach( $instructor_list as $course_instructor)
                            {   
                                $count++;                                                                                
                                echo esc_html($course_instructor['display_name'].' '.$course_instructor['llms_degrees_certs']);                            
                                if ($count < $total_instructors) {
                                    echo ' and '; // Add & if not the last author
                                }                                                    
                            }
                        }
                ?>
            </p>
            </div>

            <hr class="my-4">

            <form id="course-evaluation-form" class="survey-form" data-type="course">
                <input type="hidden" name="action" value="submit_course_evaluation">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="survey_type" value="course_feedback">
                
                <!-- Question 1: Professional Status -->
                <div class="question-block">
                    <div class="question-title">What is your current professional status?</div>
                    <div class="checkbox-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r10" value="1" id="r10">
                            <label class="form-check-label" for="r10">Psychologist</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r11" value="1" id="r11">
                            <label class="form-check-label" for="r11">Social Worker</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r12" value="1" id="r12">
                            <label class="form-check-label" for="r12">Marriage and Family Therapist</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r14" value="1" id="r14">
                            <label class="form-check-label" for="r14">Counselor</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r13" value="1" id="r13">
                            <label class="form-check-label" for="r13">Other</label>
                            <input type="text" class="form-control mt-2" name="r13text" placeholder="Please specify">
                        </div>
                    </div>
                </div>

                <!-- Question 2: Reasons for Taking Course -->
                <div class="question-block">
                    <div class="question-title">What were your reasons for taking this course?</div>
                    <div class="checkbox-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r20" value="1" id="r20">
                            <label class="form-check-label" for="r20">Subject was of interest</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r21" value="1" id="r21">
                            <label class="form-check-label" for="r21">Reputation of author</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r22" value="1" id="r22">
                            <label class="form-check-label" for="r22">Important to job activities</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r23" value="1" id="r23">
                            <label class="form-check-label" for="r23">Needed CE Hours</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="r24" value="1" id="r24">
                            <label class="form-check-label" for="r24">Other</label>
                            <input type="text" class="form-control mt-2" name="r24text" placeholder="Please specify">
                        </div>
                    </div>
                </div>

                <!-- Question 3: Overall Quality -->
                <div class="question-block">
                    <div class="question-title">How would you rate the overall quality of this course?</div>
                    <div class="radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r30" value="4" id="r30_4">
                            <label class="form-check-label" for="r30_4">Excellent</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r30" value="3" id="r30_3">
                            <label class="form-check-label" for="r30_3">Good</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r30" value="2" id="r30_2">
                            <label class="form-check-label" for="r30_2">Fair</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="r30" value="1" id="r30_1">
                            <label class="form-check-label" for="r30_1">Poor</label>
                        </div>
                    </div>
                </div>

                <!-- Learning Objectives Section -->
                <div class="question-block">
                    <div class="question-title" style="font-size: 1.3rem; color: var(--primary-blue); margin-bottom: 20px;">
                        Learning Objectives - Were the following learning objectives met?
                    </div>
                    <p class="text-muted mb-3" style="font-style: italic;">
                        After completing this course, mental health professionals will be able to:
                    </p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60%;">Learning Objective</th>
                                    <th class="text-center" style="width: 8%;">1<br><small>(Absolutely not)</small></th>
                                    <th class="text-center" style="width: 8%;">2</th>
                                    <th class="text-center" style="width: 8%;">3<br><small>(Uncertain)</small></th>
                                    <th class="text-center" style="width: 8%;">4</th>
                                    <th class="text-center" style="width: 8%;">5<br><small>(Absolutely)</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1. Discuss five appropriate steps for addressing unethical acts by a supervisee or a colleague.</td>
                                    <td class="text-center"><input type="radio" name="r12753" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r12753" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r12753" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r12753" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r12753" value="5"></td>
                                </tr>
                                <tr>
                                    <td>2. List two steps to improve ethical decisions made under emergency and crisis conditions.</td>
                                    <td class="text-center"><input type="radio" name="r12752" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r12752" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r12752" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r12752" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r12752" value="5"></td>
                                </tr>
                                <tr>
                                    <td>3. Utilize a step-by-step strategy in making ethical decisions.</td>
                                    <td class="text-center"><input type="radio" name="r12751" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r12751" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r12751" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r12751" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r12751" value="5"></td>
                                </tr>
                                <tr>
                                    <td>4. Describe five types of unethical mental health professionals.</td>
                                    <td class="text-center"><input type="radio" name="r12750" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r12750" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r12750" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r12750" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r12750" value="5"></td>
                                </tr>
                                <tr>
                                    <td>5. List four core values for keeping one's practice ethically healthy.</td>
                                    <td class="text-center"><input type="radio" name="r12748" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r12748" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r12748" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r12748" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r12748" value="5"></td>
                                </tr>
                                <tr>
                                    <td>6. Consider cultural differences and values when making ethical decisions.</td>
                                    <td class="text-center"><input type="radio" name="r12749" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r12749" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r12749" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r12749" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r12749" value="5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Your Learning Experience Section -->
                <div class="question-block">
                    <div class="question-title" style="font-size: 1.3rem; color: var(--primary-blue); margin-bottom: 20px;">
                        Your Learning Experience
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60%;">Question</th>
                                    <th class="text-center" style="width: 8%;">1<br><small>(Absolutely not)</small></th>
                                    <th class="text-center" style="width: 8%;">2</th>
                                    <th class="text-center" style="width: 8%;">3<br><small>(Uncertain)</small></th>
                                    <th class="text-center" style="width: 8%;">4</th>
                                    <th class="text-center" style="width: 8%;">5<br><small>(Absolutely)</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Was the course taught at the promised level?</td>
                                    <td class="text-center"><input type="radio" name="r80" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r80" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r80" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r80" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r80" value="5"></td>
                                </tr>
                                <tr>
                                    <td>Was the course material clear and well-organized?</td>
                                    <td class="text-center"><input type="radio" name="r200" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r200" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r200" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r200" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r200" value="5"></td>
                                </tr>
                                <tr>
                                    <td>Did the course present current developments in the field?</td>
                                    <td class="text-center"><input type="radio" name="r210" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r210" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r210" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r210" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r210" value="5"></td>
                                </tr>
                                <tr>
                                    <td>How much did you learn as a result of the CE program (1 being very little - 5 being a great deal)?</td>
                                    <td class="text-center"><input type="radio" name="r240" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r240" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r240" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r240" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r240" value="5"></td>
                                </tr>
                                <tr>
                                    <td>Would you take another course prepared by this author?</td>
                                    <td class="text-center"><input type="radio" name="r100" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r100" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r100" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r100" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r100" value="5"></td>
                                </tr>
                                <tr>
                                    <td>If you were seeking accommodations for a disability, were you satisfied?</td>
                                    <td class="text-center"><input type="radio" name="r360" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r360" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r360" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r360" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r360" value="5"></td>
                                </tr>
                                <tr>
                                    <td>Does the length of time to complete the course match the number of CE Hours awarded?</td>
                                    <td class="text-center"><input type="radio" name="r370" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r370" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r370" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r370" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r370" value="5"></td>
                                </tr>
                                <tr>
                                    <td>How useful was the content of this CE program for your practice or other professional development (1 being not useful - 5 being extremely useful)?</td>
                                    <td class="text-center"><input type="radio" name="r380" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r380" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r380" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r380" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r380" value="5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Our Service Section -->
                <div class="question-block">
                    <div class="question-title" style="font-size: 1.3rem; color: var(--primary-blue); margin-bottom: 20px;">
                        Our Service
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60%;">Question</th>
                                    <th class="text-center" style="width: 8%;">1<br><small>(Absolutely not)</small></th>
                                    <th class="text-center" style="width: 8%;">2</th>
                                    <th class="text-center" style="width: 8%;">3<br><small>(Uncertain)</small></th>
                                    <th class="text-center" style="width: 8%;">4</th>
                                    <th class="text-center" style="width: 8%;">5<br><small>(Absolutely)</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Did you find the website and course material user friendly?</td>
                                    <td class="text-center"><input type="radio" name="r230" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r230" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r230" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r230" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r230" value="5"></td>
                                </tr>
                                <tr>
                                    <td>Would you take another course from ContinuingEdCourses.Net?</td>
                                    <td class="text-center"><input type="radio" name="r110" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r110" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r110" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r110" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r110" value="5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Decision Factors Section -->
                <div class="question-block">
                    <div class="question-title" style="font-size: 1.3rem; color: var(--primary-blue); margin-bottom: 20px;">
                        How important were the following in your decision to take courses from us?
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60%;">Factor</th>
                                    <th class="text-center" style="width: 8%;">1<br><small>(Not Important)</small></th>
                                    <th class="text-center" style="width: 8%;">2</th>
                                    <th class="text-center" style="width: 8%;">3<br><small>(Uncertain)</small></th>
                                    <th class="text-center" style="width: 8%;">4</th>
                                    <th class="text-center" style="width: 8%;">5<br><small>(Very Important)</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Reputation of the authors</td>
                                    <td class="text-center"><input type="radio" name="r300" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r300" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r300" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r300" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r300" value="5"></td>
                                </tr>
                                <tr>
                                    <td>Quality of the courses</td>
                                    <td class="text-center"><input type="radio" name="r310" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r310" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r310" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r310" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r310" value="5"></td>
                                </tr>
                                <tr>
                                    <td>Ease of use of the website</td>
                                    <td class="text-center"><input type="radio" name="r320" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r320" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r320" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r320" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r320" value="5"></td>
                                </tr>
                                <tr>
                                    <td>You reminded me with a postcard or ad</td>
                                    <td class="text-center"><input type="radio" name="r330" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r330" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r330" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r330" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r330" value="5"></td>
                                </tr>
                                <tr>
                                    <td>CERewards program discounts</td>
                                    <td class="text-center"><input type="radio" name="r340" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r340" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r340" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r340" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r340" value="5"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="r350text" class="form-label">Other:</label>
                                        <input type="text" class="form-control" name="r350text" id="r350text" placeholder="Please specify">
                                    </td>
                                    <td class="text-center"><input type="radio" name="r350" value="1"></td>
                                    <td class="text-center"><input type="radio" name="r350" value="2"></td>
                                    <td class="text-center"><input type="radio" name="r350" value="3"></td>
                                    <td class="text-center"><input type="radio" name="r350" value="4"></td>
                                    <td class="text-center"><input type="radio" name="r350" value="5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Additional Comments -->
                <div class="question-block">
                    <div class="question-title">What do you think of our CERewards program?</div>
                    <textarea class="form-control" name="r340text" rows="4" placeholder="Your feedback about CERewards program..."></textarea>
                </div>

                <div class="question-block">
                    <div class="question-title">Please provide additional comments/complaints:</div>
                    <textarea class="form-control" name="r120text" rows="4" placeholder="Your comments or complaints..."></textarea>
                </div>

                <div class="question-block">
                    <div class="question-title">Please provide suggestions for future CE courses:</div>
                    <textarea class="form-control" name="r130text" rows="4" placeholder="Your suggestions for future courses..."></textarea>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="submit" name="SubmitSurvey" class="btn btn-submit" id="submitBtn">
                        <i class="bi bi-send me-2"></i>Submit Evaluation
                    </button>
                </div>

                <div class="note-box mt-4">
                    <strong>Note:</strong> Your feedback is valuable to us and will be shared with the course author to help improve future courses. Thank you for taking the time to complete this evaluation!
                </div>
            </form>
            <?php 
            }
            ?>
        </div>
    </div>
<?php
get_footer();
?>