<?php
/**
 * Template Name: Page Proposal Author
 * Template Post Type: page
 * Description:  Page Proposal Author
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>
<div class="container mb-5">
            <!-- Page Header -->
            <div class="header-section text-center">
                <h1 class="main-title">Course Proposal Submission</h1>
                <p class="text-muted mt-2 mb-0">Share your expertise with healthcare professionals worldwide</p>
            </div>

            <!-- Introduction Box -->
            <div class="note-section">
                <p><strong><i class="bi bi-info-circle-fill me-2"></i>Before You Begin:</strong></p>
                <p class="mb-2">Thank you for your interest in publishing a course with ContinuingEdCourses.Net. Please review the following requirements:</p>
                <ul class="mb-2">
                    <li>All courses must be at a <strong>post-doctoral level</strong></li>
                    <li>Courses typically range from 1-6 credit hours (1 hour ≈ 6,000 words)</li>
                    <li>Include at least <strong>5 peer-reviewed references</strong> in APA 7th Edition format</li>
                    <li>Address <strong>cultural diversity</strong> considerations relevant to your topic</li>
                    <li>If this is your first submission, please attach your <strong>Curriculum Vitae</strong></li>
                </ul>
                <p class="mb-0">Fields marked with <span class="required">*</span> are required.</p>
            </div>

            <!-- Main Form -->
            <div class="content-card">
                <form id="authorProposalForm" enctype="multipart/form-data">
                    
                    <!-- Section 1: Personal Information -->
                    <div class="mb-5">
                        <h2 class="section-heading"><i class="bi bi-person-fill"></i> Personal Information</h2>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fullName" class="form-label">Full Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="fullName" placeholder="Dr. John Doe" required>
                                <small class="text-muted">Include your professional title (e.g., Dr., Ph.D., LCSW)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" id="email" placeholder="john.doe@example.com" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="required">*</span></label>
                                <input type="tel" class="form-control" id="phone" placeholder="+1-123-456-7890" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address (Optional)</label>
                                <input type="text" class="form-control" id="address" placeholder="City, State, Country">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Course Summary -->
                    <div class="mb-5">
                        <h2 class="section-heading"><i class="bi bi-book-fill"></i> Course Summary</h2>
                        
                        <div class="mb-3">
                            <label for="courseName" class="form-label">Course Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="courseName" placeholder="e.g., Advanced Clinical Psychology" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="courseLevel" class="form-label">Course Level <span class="required">*</span></label>
                                <select class="form-select" id="courseLevel" required>
                                    <option value="">Select level...</option>
                                    <option value="introductory">Introductory</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="creditHours" class="form-label">Credit Hours <span class="required">*</span></label>
                                <input type="number" class="form-control" id="creditHours" min="1" max="10" placeholder="e.g., 3" required>
                                <small class="text-muted">Suggested: 1 credit hour ≈ 6,000 words</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="courseDescription" class="form-label">Course Description <span class="required">*</span></label>
                            <textarea class="form-control" id="courseDescription" rows="5" placeholder="Provide a brief description of the course purpose and main content (max 200 words)" required></textarea>
                            <small class="text-muted">Characters remaining: <span id="descCharCount">1200</span>/1200</small>
                        </div>
                    </div>

                    <!-- Section 3: Learning Objectives -->
                    <div class="mb-5">
                        <h2 class="section-heading"><i class="bi bi-target"></i> Learning Objectives</h2>
                        
                        <div class="mb-3">
                            <label for="learningObjectives" class="form-label">Learning Objectives <span class="required">*</span></label>
                            <textarea class="form-control" id="learningObjectives" rows="6" placeholder="List 3-5 measurable learning objectives. Use action verbs such as 'analyze', 'evaluate', 'apply', 'synthesize', etc.&#10;&#10;Example:&#10;• Analyze the impact of cultural factors on psychological treatment outcomes&#10;• Evaluate evidence-based interventions for specific clinical populations&#10;• Apply ethical decision-making frameworks in complex clinical scenarios" required></textarea>
                            <small class="text-muted">Use measurable action verbs based on Bloom's Taxonomy</small>
                        </div>
                    </div>

                    <!-- Section 4: Course Outline -->
                    <div class="mb-5">
                        <h2 class="section-heading"><i class="bi bi-list-ol"></i> Course Outline</h2>
                        
                        <div class="mb-3">
                            <label for="courseOutline" class="form-label">Course Outline <span class="required">*</span></label>
                            <textarea class="form-control" id="courseOutline" rows="8" placeholder="Provide the main topics and key points for each topic.&#10;&#10;Example:&#10;I. Introduction to Clinical Assessment&#10;   A. Historical perspectives&#10;   B. Current best practices&#10;&#10;II. Assessment Tools and Techniques&#10;   A. Standardized instruments&#10;   B. Clinical interviews&#10;   C. Observational methods" required></textarea>
                        </div>
                       
                    </div>

                    <!-- Section 5: Cultural Diversity -->
                    <div class="mb-5">
                        <h2 class="section-heading"><i class="bi bi-globe"></i> Cultural Diversity Integration</h2>
                        
                        <div class="mb-3">
                            <label for="culturalDiversity" class="form-label">How does your course address cultural diversity? <span class="required">*</span></label>
                            <textarea class="form-control" id="culturalDiversity" rows="6" placeholder="Describe how the course integrates cultural diversity considerations.&#10;&#10;Examples:&#10;• Discussion of treatment approaches across different ethnic groups&#10;• Examination of cultural factors in diagnosis and assessment&#10;• Consideration of socioeconomic and linguistic diversity&#10;• Analysis of health disparities among diverse populations" required></textarea>
                        </div>
                    </div>

                    <!-- Section 6: References -->
                    <div class="mb-5">
                        <h2 class="section-heading"><i class="bi bi-journal-text"></i> References</h2>
                        <p class="text-muted mb-3">Provide at least 5 peer-reviewed references in APA 7th Edition format</p>
                        
                        <div id="referencesContainer">
                            <div class="mb-3">
                                <label for="reference1" class="form-label">Reference 1 <span class="required">*</span></label>
                                <textarea class="form-control" id="reference1" rows="3" placeholder="Author, A. A., & Author, B. B. (Year). Title of article. Journal Name, Volume(Issue), pages. https://doi.org/xxx" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="reference2" class="form-label">Reference 2 <span class="required">*</span></label>
                                <textarea class="form-control" id="reference2" rows="3" placeholder="Author, A. A., & Author, B. B. (Year). Title of article. Journal Name, Volume(Issue), pages. https://doi.org/xxx" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="reference3" class="form-label">Reference 3 <span class="required">*</span></label>
                                <textarea class="form-control" id="reference3" rows="3" placeholder="Author, A. A., & Author, B. B. (Year). Title of article. Journal Name, Volume(Issue), pages. https://doi.org/xxx" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="reference4" class="form-label">Reference 4 <span class="required">*</span></label>
                                <textarea class="form-control" id="reference4" rows="3" placeholder="Author, A. A., & Author, B. B. (Year). Title of article. Journal Name, Volume(Issue), pages. https://doi.org/xxx" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="reference5" class="form-label">Reference 5 <span class="required">*</span></label>
                                <textarea class="form-control" id="reference5" rows="3" placeholder="Author, A. A., & Author, B. B. (Year). Title of article. Journal Name, Volume(Issue), pages. https://doi.org/xxx" required></textarea>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addReferenceBtn">
                            <i class="bi bi-plus-circle"></i> Add Another Reference
                        </button>
                        
                    </div>

                    <!-- Section 7: Curriculum Vitae -->
                    <div class="mb-5">
                        <h2 class="section-heading"><i class="bi bi-file-earmark-person"></i> Curriculum Vitae</h2>
                                               

                        <div class="mb-3" id="cvUploadSection">
                            <label for="cvFile" class="form-label">Upload Curriculum Vitae <span class="required">*</span></label>
                            <input type="file" class="form-control" id="cvFile" accept=".pdf,.doc,.docx">
                            <small class="text-muted">PDF or Word format, max 5MB</small>
                        </div>
                    </div>

                    <!-- Section 8: Conflict of Interest -->
                    <div class="mb-5">
                        <h2 class="section-heading"><i class="bi bi-exclamation-triangle"></i> Conflict of Interest</h2>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="conflictOfInterest">
                                <label class="form-check-label" for="conflictOfInterest">
                                    <strong>I have a conflict of interest to disclose</strong>
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2">Examples: Financial relationships with commercial entities, research funding, consulting fees, stock ownership, or other relationships that could influence the course content.</small>
                        </div>

                        <div class="mb-3" id="conflictExplanation" style="display: none;">
                            <label for="conflictDetails" class="form-label">Please explain your conflict of interest <span class="required">*</span></label>
                            <textarea class="form-control" id="conflictDetails" rows="4" placeholder="Describe the nature of the conflict, including any financial relationships, research funding, or other relevant interests"></textarea>
                        </div>
                    </div>

                    <!-- Section 9: APA Required Statement -->
                    <div class="mb-5">
                        <h2 class="section-heading"><i class="bi bi-shield-check"></i> APA Required Statement</h2>
                        
                        <div class="highlight-box mb-3">
                            <span class="highlight-title">APA Statement Examples</span>
                            <p class="small mb-2"><strong>Example 1 (Empirical Research):</strong></p>
                            <p class="small mb-3" style="font-style: italic;">"The methods and interventions discussed in this course are based on peer-reviewed empirical research and established clinical guidelines. While the evidence supports their effectiveness, individual outcomes may vary. Practitioners should consider client-specific factors, cultural contexts, and clinical judgment when applying these approaches. The author acknowledges limitations in generalizing findings across diverse populations and settings."</p>
                            
                            <p class="small mb-2"><strong>Example 2 (Theoretical/Review):</strong></p>
                            <p class="small" style="font-style: italic;">"This course presents current theoretical perspectives and research findings in the field. The content is grounded in peer-reviewed literature; however, some areas reflect ongoing debate and emerging evidence. Practitioners should remain aware that clinical applications require careful consideration of individual circumstances, professional judgment, and adherence to ethical standards. The author recognizes that evidence continues to evolve and encourages ongoing professional development."</p>
                        </div>

                        <div class="mb-3">
                            <label for="apaStatement" class="form-label">Your APA Statement <span class="required">*</span></label>
                            <textarea class="form-control" id="apaStatement" rows="6" placeholder="Based on the examples above, write your statement addressing:&#10;• The scientific/empirical basis of your course content&#10;• Any limitations or areas of uncertainty&#10;• Considerations for practical application&#10;• Potential risks or cautions for practitioners" required></textarea>
                            <small class="text-muted">This statement ensures ethical presentation of content and manages participant expectations</small>
                        </div>
                    </div>

                    <!-- Agreement Section -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="agreementCheck" id="agreementCheck" required>
                            <label class="form-check-label" for="agreementCheck">
                                <strong>I certify that all information provided is accurate and complete <span class="required">*</span></strong>
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">By submitting this proposal, I agree to ContinuingEdCourses.Net's author guidelines and understand that my proposal will be reviewed by our advisory board.</small>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-custom btn-lg">
                            <i class="bi bi-send-fill me-2"></i>Submit Proposal
                        </button>
                    </div>

                    <div id="formMessage" class="mt-4"></div>
                </form>
            </div>

            <!-- Additional Information -->
            <div class="content-card">
                <h2 class="section-heading"><i class="bi bi-question-circle"></i> What Happens Next?</h2>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="step-item">
                            <span class="step-number">1</span>
                            <span class="step-text">We'll review your proposal within 2-3 business days</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="step-item">
                            <span class="step-number">2</span>
                            <span class="step-text">Our advisory board will evaluate content quality and relevance</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="step-item">
                            <span class="step-number">3</span>
                            <span class="step-text">You'll receive feedback and next steps via email</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="step-item">
                            <span class="step-number">4</span>
                            <span class="step-text">If approved, we'll discuss contract terms and timeline</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php
get_footer();
?>

