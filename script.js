// DOM Elements
const loginCard = document.querySelector('.login-card');
const registerCard = document.querySelector('.register-card');
const showRegisterLink = document.getElementById('showRegister');
const showLoginLink = document.getElementById('showLogin');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const adminLoginForm = document.getElementById('adminLoginForm');

// Toggle between login and register forms - only add event listeners if elements exist
if (showRegisterLink) {
    showRegisterLink.addEventListener('click', (e) => {
        e.preventDefault();
        loginCard.style.display = 'none';
        registerCard.style.display = 'block';
    });
}

if (showLoginLink) {
    showLoginLink.addEventListener('click', (e) => {
        e.preventDefault();
        registerCard.style.display = 'none';
        loginCard.style.display = 'block';
    });
}

// Tab switching functionality
function showUserLogin() {
    document.getElementById('userTabBtn').classList.add('active');
    document.getElementById('adminTabBtn').classList.remove('active');
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('adminLoginForm').style.display = 'none';
    document.getElementById('adminNote').style.display = 'none';
    document.getElementById('registerLink').style.display = 'block';
}

function showAdminLogin() {
    document.getElementById('adminTabBtn').classList.add('active');
    document.getElementById('userTabBtn').classList.remove('active');
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('adminLoginForm').style.display = 'block';
    document.getElementById('adminNote').style.display = 'block';
    document.getElementById('registerLink').style.display = 'none';
}

// Handle login form submission
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(loginForm);
        const email = formData.get('email');
        const password = formData.get('password');
    
    try {
        console.log('Sending login request...');
        const response = await fetch('auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=login&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            localStorage.setItem('user', JSON.stringify(data.user));
            window.location.href = 'dashboard.html';
        } else {
            alert(data.message || 'Login failed');
        }
    } catch (error) {
        console.error('Login error:', error);
        alert('An error occurred. Please try again.');
    }
  });
}

// Handle register form submission
if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(registerForm);
        const name = formData.get('name');
        const email = formData.get('email');
        const password = formData.get('password');
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=register&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Registration successful! Please login.');
            registerCard.style.display = 'none';
            loginCard.style.display = 'block';
            registerForm.reset();
        } else {
            alert(data.message || 'Registration failed');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
  });
}

// Handle admin login form submission
if (adminLoginForm) {
    adminLoginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(adminLoginForm);
        const email = formData.get('email');
        const password = formData.get('password');
        
        try {
            const response = await fetch('auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=login&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            });
            
            const data = await response.json();
            
            if (data.success && data.user.is_admin) {
                localStorage.setItem('user', JSON.stringify(data.user));
                window.location.href = 'admin.html';
            } else if (data.success && !data.user.is_admin) {
                alert('This account is not an admin account.');
            } else {
                alert(data.message || 'Admin login failed');
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    });
}

// Dashboard functionality
// Auto-load dashboard when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded event fired');
    console.log('Current pathname:', window.location.pathname);
    
    // Check if we're on the dashboard page
    if (window.location.pathname.includes('dashboard.html') || document.querySelector('.dashboard')) {
        console.log('Loading dashboard...');
        loadDashboard();
    }
    
    // Check if we're on the module detail page
    if (window.location.pathname.includes('module.html')) {
        console.log('Loading module detail...');
        loadModuleDetail();
    }
});

function loadDashboard() {
    const user = JSON.parse(localStorage.getItem('user'));
    if (!user) {
        window.location.href = 'index.html';
        return;
    }
    
    // Admin button has been removed as requested
    
    // Load user progress
    loadUserProgress();
    loadModules();
    loadVideos();
}

function loadUserProgress() {
    const user = JSON.parse(localStorage.getItem('user'));
    
    // Check if user exists and has id property
    if (!user || !user.id) {
        console.error('User not logged in or missing ID');
        return;
    }
    
    console.log('Loading user progress for user ID:', user.id);
    
    // Use POST method as it was originally designed
    fetch('progress.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user_id=${user.id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateProgressDisplay(data.progress);
        }
    })
    .catch(error => console.error('Error loading progress:', error));
}

function updateProgressDisplay(progress) {
    // Update overall progress
    const overallProgressFill = document.querySelector('.overall-progress .progress-fill');
    const overallProgressText = document.querySelector('.overall-progress .progress-text');
    
    if (progress && progress.length > 0) {
        // Count completed modules (those with 100% progress)
        const completedModules = progress.filter(module => module.percentage === 100).length;
        
        // If no modules are completed, show 0%
        const completionPercentage = completedModules > 0 ? Math.round((completedModules / progress.length) * 100) : 0;
        
        if (overallProgressFill) {
            overallProgressFill.style.width = `${completionPercentage}%`;
        }
        if (overallProgressText) {
            overallProgressText.textContent = `${completionPercentage}% Complete`;
        }
    }
    
    // Update individual module progress bars
    const progressElements = document.querySelectorAll('.module-card .progress-fill');
    const progressTexts = document.querySelectorAll('.module-card .progress-text');
    
    progress.forEach((moduleProgress, index) => {
        // Ensure percentage is a number
        const percentage = typeof moduleProgress.percentage === 'number' ? moduleProgress.percentage : 0;
        
        if (progressElements[index]) {
            progressElements[index].style.width = `${percentage}%`;
        }
        if (progressTexts[index]) {
            progressTexts[index].textContent = `${percentage}% Complete`;
        }
    });
}

function loadModules() {
    console.log('Loading modules...');
    
    // Get user ID from local storage
    const user = JSON.parse(localStorage.getItem('user'));
    const userId = user ? user.id : null;
    
    // Add user_id as a query parameter if available
    const url = userId ? `modules.php?user_id=${userId}` : 'modules.php';
    
    console.log('Fetching modules with URL:', url);
    
    fetch(url)
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Modules data:', data);
        if (data.success) {
            displayModules(data.modules);
        } else {
            console.error('Modules error:', data.message);
            // Try test endpoint as fallback
            fetch('test_modules.php')
            .then(response => response.json())
            .then(testData => {
                console.log('Test modules data:', testData);
                if (testData.success) {
                    displayModules(testData.modules);
                }
            })
            .catch(testError => console.error('Test modules error:', testError));
        }
    })
    .catch(error => {
        console.error('Error loading modules:', error);
        // Try test endpoint as fallback
        fetch('test_modules.php')
        .then(response => response.json())
        .then(testData => {
            console.log('Test modules data:', testData);
            if (testData.success) {
                displayModules(testData.modules);
            }
        })
        .catch(testError => console.error('Test modules error:', testError));
    });
}

function displayModules(modules) {
    const modulesGrid = document.querySelector('.modules-grid');
    if (!modulesGrid) return;
    
    modulesGrid.innerHTML = '';
    
    if (!modules || modules.length === 0) {
        modulesGrid.innerHTML = '<p style="color: red;">No modules found. Please check database connection.</p>';
        return;
    }
    
    modules.forEach(module => {
        const moduleCard = document.createElement('div');
        moduleCard.className = 'module-card';
        moduleCard.onclick = () => openModule(module.id);
        
        // Ensure progress is a number and has a default value of 0
        const progressValue = typeof module.progress === 'number' ? module.progress : 0;
        
        moduleCard.innerHTML = `
            <h3>${module.title}</h3>
            <p>${module.description}</p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: ${progressValue}%"></div>
            </div>
            <div class="progress-text">${progressValue}% Complete</div>
        `;
        
        modulesGrid.appendChild(moduleCard);
    });
}

function loadVideos() {
    console.log('Loading videos...');
    fetch('videos.php')
    .then(response => {
        console.log('Videos response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Videos data:', data);
        if (data.success) {
            displayVideos(data.videos);
        } else {
            console.error('Videos error:', data.message);
        }
    })
    .catch(error => {
        console.error('Error loading videos:', error);
        // Show error message to user
        const videoGrid = document.querySelector('.video-grid');
        if (videoGrid) {
            videoGrid.innerHTML = '<p style="color: red;">Error loading videos. Please check console for details.</p>';
        }
    });
}

function displayVideos(videos) {
    const videoGrid = document.querySelector('.video-grid');
    if (!videoGrid) return;
    
    videoGrid.innerHTML = '';
    
    videos.forEach(video => {
        const videoCard = document.createElement('div');
        videoCard.className = 'video-card';
        
        videoCard.innerHTML = `
            <h4>${video.title}</h4>
            <p>${video.description}</p>
            <button onclick="playVideo('${video.url}')" class="login-btn">Watch Video</button>
        `;
        
        videoGrid.appendChild(videoCard);
    });
}

function openModule(moduleId) {
    window.location.href = `module.html?id=${moduleId}`;
}

function playVideo(videoUrl) {
    // Open video in a new window/tab
    if (videoUrl && videoUrl.trim() !== '') {
        window.open(videoUrl, '_blank');
    } else {
        alert('No video URL available');
    }
}

function logout() {
    localStorage.removeItem('user');
    window.location.href = 'index.html';
}

// Module detail functionality
function loadModuleDetail() {
    const urlParams = new URLSearchParams(window.location.search);
    const moduleId = urlParams.get('id');
    
    console.log('Module ID:', moduleId);
    
    // Check if user is logged in
    const user = JSON.parse(localStorage.getItem('user'));
    if (!user || !user.id) {
        console.log('User not logged in, redirecting to login page');
        alert('You need to be logged in to view this module');
        window.location.href = 'index.html'; // Redirect to index.html which has the login form
        return;
    }
    
    if (!moduleId) {
        console.log('No module ID found, redirecting to dashboard');
        window.location.href = 'dashboard.html';
        return;
    }
    
    console.log('Fetching module content for ID:', moduleId);
    fetch(`module_content.php?id=${moduleId}`)
    .then(response => {
        console.log('Module content response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Module content data:', data);
        if (data.success) {
            displayModuleContent(data.module);
        } else {
            console.error('Module content error:', data.message);
        }
    })
    .catch(error => {
        console.error('Error loading module:', error);
        // Show error message to user
        const pptContent = document.getElementById('ppt-content');
        const videoContent = document.getElementById('video-content');
        if (pptContent) {
            pptContent.innerHTML = '<p style="color: red;">Error loading module content. Please check console for details.</p>';
        }
        if (videoContent) {
            videoContent.innerHTML = '<p style="color: red;">Error loading module content. Please check console for details.</p>';
        }
    });
}

// 
function displayModuleContent(module) {
    console.log('displayModuleContent called with module:', module);

    const moduleTitle = document.querySelector('.module-detail h2');
    if (moduleTitle) {
        moduleTitle.textContent = module.title;
        console.log('Updated module title to:', module.title);
    }

    // Load PPT content only once
    const pptContent = document.getElementById('ppt-content');
    if (pptContent) {
        // Clear the current content
        pptContent.innerHTML = '';

        // Only append PPT content if available
        if (module.ppt_content && module.ppt_content.trim() !== '') {
            pptContent.innerHTML = module.ppt_content;
        } else {
            pptContent.innerHTML = '<p>No PPT content available</p>';
        }
        console.log('PPT content updated');
    } else {
        console.error('PPT content element not found');
    }

    // Load video content
    const videoContent = document.getElementById('video-content');
    if (videoContent) {
        // Clear the current content
        videoContent.innerHTML = '';

        // Only append video content if available
        if (module.video_content && module.video_content.trim() !== '') {
            videoContent.innerHTML = module.video_content;
        } else {
            videoContent.innerHTML = '<p>No video content available</p>';
        }
        console.log('Video content updated');
    } else {
        console.error('Video content element not found');
    }
}

function switchTab(tabName) {
    // Hide all content sections
    const contentSections = document.querySelectorAll('.content-section');
    contentSections.forEach(section => section.classList.remove('active'));
    
    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(btn => btn.classList.remove('active'));
    
    // Show selected content section
    const selectedSection = document.getElementById(`${tabName}-content`);
    if (selectedSection) {
        selectedSection.classList.add('active');
    }
    
    // Add active class to clicked tab button
    const clickedButton = document.querySelector(`[onclick="switchTab('${tabName}')"]`);
    if (clickedButton) {
        clickedButton.classList.add('active');
    }
}

function completeModule() {
    const urlParams = new URLSearchParams(window.location.search);
    const moduleId = urlParams.get('id');
    const user = JSON.parse(localStorage.getItem('user'));
    
    // Check if user exists and has id property
    if (!user || !user.id) {
        alert('You need to be logged in to complete this module');
        window.location.href = 'index.html';
        return;
    }
    
    fetch('complete_module.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user_id=${user.id}&module_id=${moduleId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Module completed successfully!');
            window.location.href = 'dashboard.html';
        } else {
            alert(data.message || 'Failed to complete module');
        }
    })
    .catch(error => {
        alert('An error occurred. Please try again.');
    });
}

// Certificate functionality
function generateCertificate() {
    const user = JSON.parse(localStorage.getItem('user'));
    
    // Check if user exists and has id property
    if (!user || !user.id) {
        alert('You need to be logged in to generate a certificate');
        window.location.href = 'index.html';
        return;
    }
    
    fetch('generate_certificate.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user_id=${user.id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayCertificate(data.certificate);
        } else {
            alert(data.message || 'Failed to generate certificate');
        }
    })
    .catch(error => {
        alert('An error occurred. Please try again.');
    });
}

function displayCertificate(certificate) {
    const certificateContainer = document.querySelector('.certificate');
    if (certificateContainer) {
        certificateContainer.innerHTML = `
            <h1>Certificate of Completion</h1>
            <p>This is to certify that</p>
            <h2>${certificate.user_name}</h2>
            <p>has successfully completed the AI Training Program</p>
            <p>Date: ${certificate.completion_date}</p>
            <p>Certificate ID: ${certificate.certificate_id}</p>
            <button onclick="downloadCertificate()" class="download-btn">Download Certificate</button>
        `;
    }
}

function downloadCertificate() {
    // In a real application, this would generate and download a PDF
    alert('Certificate download started!');
}

// Initialize based on current page
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop();
    
    if (currentPage === 'dashboard.html') {
        loadDashboard();
    } else if (currentPage === 'module.html') {
        loadModuleDetail();
    } else if (currentPage === 'certificate.html') {
        generateCertificate();
    }
});