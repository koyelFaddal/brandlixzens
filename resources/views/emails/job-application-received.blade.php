<h2>New Job Application</h2>
<p><strong>Job:</strong> {{ $application->job_title_snapshot }}</p>
<p><strong>Name:</strong> {{ $application->full_name }}</p>
<p><strong>Email:</strong> {{ $application->email }}</p>
<p><strong>Phone:</strong> {{ $application->phone_number }}</p>
<p><strong>LinkedIn:</strong> {{ $application->linkedin_url ?: '-' }}</p>
<p><strong>GitHub:</strong> {{ $application->github_url ?: '-' }}</p>
<p><strong>Portfolio:</strong> {{ $application->portfolio_url ?: '-' }}</p>
<p><strong>Current CTC:</strong> {{ $application->present_salary ?: '-' }}</p>
<p><strong>Expected CTC:</strong> {{ $application->expected_salary ?: '-' }}</p>
<p><strong>Experience:</strong> {{ $application->years_of_experience ?: '-' }}</p>
<p><a href="{{ $resumeUrl }}">Download CV</a> (link valid for 7 days)</p>
