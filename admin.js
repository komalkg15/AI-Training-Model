// Admin Panel JavaScript

// Check if user is admin
function checkAdminAccess() {
    const user = JSON.parse(localStorage.getItem('user'));
    if (!user || !user.is_admin) {
        alert('Access denied. Admin privileges required.');
        window.location.href = 'dashboard.html';
        return false;
    }
    return true;
}

// Navigation
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Remove active class from all nav buttons
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(`${sectionName}-section`).classList.add('active');
    
    // Add active class to clicked nav button
    event.target.classList.add('active');
    
    // Load section data
    loadSectionData(sectionName);
}

function loadSectionData(sectionName) {
    switch(sectionName) {
        case 'modules':
            loadModules();
            break;
        case 'videos':
            loadVideos();
            break;
    }
}

// Modules Management
function loadModules() {
    fetch('admin_modules.php?action=list')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayModules(data.modules);
        } else {
            console.error('Error loading modules:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function displayModules(modules) {
    const modulesList = document.querySelector('.modules-list');
    modulesList.innerHTML = '';
    
    if (!modules || modules.length === 0) {
        modulesList.innerHTML = '<div class="list-item"><p>No modules found.</p></div>';
        return;
    }
    
    modules.forEach(module => {
        const moduleItem = document.createElement('div');
        moduleItem.className = 'list-item';
        moduleItem.innerHTML = `
            <div class="item-info">
                <div class="item-title">${module.title}</div>
                <div class="item-description">${module.description}</div>
            </div>
            <div class="item-actions">
                <button onclick="editModule(${module.id})" class="edit-btn">Edit</button>
                <button onclick="deleteModule(${module.id})" class="delete-btn">Delete</button>
            </div>
        `;
        modulesList.appendChild(moduleItem);
    });
}

function showAddModuleForm() {
    document.getElementById('form-title').textContent = 'Add New Module';
    document.getElementById('module-id').value = '';
    document.getElementById('module-title').value = '';
    document.getElementById('module-description').value = '';
    document.getElementById('module-ppt').value = '';
    document.getElementById('module-video').value = '';
    document.getElementById('module-form').style.display = 'flex';
}

function editModule(moduleId) {
    fetch(`admin_modules.php?action=get&id=${moduleId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const module = data.module;
            document.getElementById('form-title').textContent = 'Edit Module';
            document.getElementById('module-id').value = module.id;
            document.getElementById('module-title').value = module.title;
            document.getElementById('module-description').value = module.description;
            document.getElementById('module-ppt').value = module.ppt_content;
            document.getElementById('module-video').value = module.video_content;
            document.getElementById('module-form').style.display = 'flex';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function closeModuleForm() {
    document.getElementById('module-form').style.display = 'none';
}

function deleteModule(moduleId) {
    if (confirm('Are you sure you want to delete this module?')) {
        fetch('admin_modules.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&id=${moduleId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Module deleted successfully!');
                loadModules();
            } else {
                alert(data.message || 'Failed to delete module');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    }
}

// Videos Management
function loadVideos() {
    fetch('admin_videos.php?action=list')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayVideos(data.videos);
        } else {
            console.error('Error loading videos:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function displayVideos(videos) {
    const videosList = document.querySelector('.videos-list');
    videosList.innerHTML = '';
    
    if (!videos || videos.length === 0) {
        videosList.innerHTML = '<div class="list-item"><p>No videos found.</p></div>';
        return;
    }
    
    videos.forEach(video => {
        const videoItem = document.createElement('div');
        videoItem.className = 'list-item';
        videoItem.innerHTML = `
            <div class="item-info">
                <div class="item-title">${video.title}</div>
                <div class="item-description">${video.description}</div>
            </div>
            <div class="item-actions">
                <button onclick="editVideo(${video.id})" class="edit-btn">Edit</button>
                <button onclick="deleteVideo(${video.id})" class="delete-btn">Delete</button>
            </div>
        `;
        videosList.appendChild(videoItem);
    });
}

function showAddVideoForm() {
    document.getElementById('video-form-title').textContent = 'Add New Video';
    document.getElementById('video-id').value = '';
    document.getElementById('video-title').value = '';
    document.getElementById('video-description').value = '';
    document.getElementById('video-url').value = '';
    document.getElementById('video-form').style.display = 'flex';
}

function editVideo(videoId) {
    fetch(`admin_videos.php?action=get&id=${videoId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const video = data.video;
            document.getElementById('video-form-title').textContent = 'Edit Video';
            document.getElementById('video-id').value = video.id;
            document.getElementById('video-title').value = video.title;
            document.getElementById('video-description').value = video.description;
            document.getElementById('video-url').value = video.url;
            document.getElementById('video-form').style.display = 'flex';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function closeVideoForm() {
    document.getElementById('video-form').style.display = 'none';
}

function deleteVideo(videoId) {
    if (confirm('Are you sure you want to delete this video?')) {
        fetch('admin_videos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&id=${videoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Video deleted successfully!');
                loadVideos();
            } else {
                alert(data.message || 'Failed to delete video');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    }
}





// Form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Check admin access
    if (!checkAdminAccess()) return;
    
    // Load initial data
    loadModules();
    
    // Module form submission
    document.getElementById('moduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const moduleId = formData.get('id');
        const action = moduleId ? 'edit' : 'add';
        
        fetch('admin_modules.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(formData) + `&action=${action}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(moduleId ? 'Module updated successfully!' : 'Module added successfully!');
                closeModuleForm();
                loadModules();
            } else {
                alert(data.message || 'Failed to save module');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    });
    
    // Video form submission
    document.getElementById('videoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const videoId = formData.get('id');
        const action = videoId ? 'edit' : 'add';
        
        fetch('admin_videos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(formData) + `&action=${action}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(videoId ? 'Video updated successfully!' : 'Video added successfully!');
                closeVideoForm();
                loadVideos();
            } else {
                alert(data.message || 'Failed to save video');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    });
});

// Logout function
function logout() {
    localStorage.removeItem('user');
    window.location.href = 'index.html';
} 