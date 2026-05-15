<?php
/**
 * Motor Fix - Blog Post Manager (Fresh Build)
 * @version 3.0
 * @date October 23, 2025
 */

require_once __DIR__ . '/ic/aheader.php';
$media_base_url = '/u/media/blog/';
?>

<!-- Libraries -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

<main class="blog-manager">
    <div class="blog-header">
        <h1>Blog Post Manager</h1>
        <p>Create and manage your blog posts</p>
    </div>

    <div class="blog-toolbar">
        <button id="btn-new-post" class="btn-primary">
            <i class="fas fa-plus"></i> New Post
        </button>
        <select id="filter-status" class="filter-select">
            <option value="all">All Posts</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
        </select>
    </div>

    <div id="posts-container" class="posts-list">
        <div class="loader">
            <div class="spinner"></div>
            <p>Loading posts...</p>
        </div>
    </div>
</main>

<!-- MODAL: Post Editor -->
<div id="modal-editor" class="modal-backdrop">
    <div class="modal-window">
        <div class="modal-header">
            <h2 id="modal-title">New Post</h2>
            <button id="btn-close-modal" class="btn-close">&times;</button>
        </div>

        <form id="form-post" class="modal-body">
            <input type="hidden" id="input-post-id" value="0">

            <div class="editor-layout">
                <!-- Main Editor -->
                <div class="editor-main">
                    <div class="form-group">
                        <label>Post Title</label>
                        <input type="text" id="input-title" placeholder="Enter title..." required>
                    </div>

                    <div class="form-group">
                        <label>Post Content</label>
                        <div id="editor-quill"></div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="editor-sidebar">
                    <!-- Settings Panel -->
                    <div class="sidebar-panel">
                        <h3>Settings</h3>
                        <div class="form-group">
                            <label>Status</label>
                            <select id="input-status">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>

                    <!-- Media Gallery Panel -->
                    <div class="sidebar-panel media-panel">
                        <h3>Media Gallery</h3>
                        <p class="panel-hint">Upload images or videos. First item is featured.</p>
                        
                        <input type="file" id="input-filepond" name="filepond" multiple>

                        <div id="gallery-preview" class="gallery-grid">
                            <p class="gallery-empty">No media uploaded</p>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="modal-footer">
                <div id="save-status" class="save-status"></div>
                <button type="button" id="btn-cancel" class="btn-secondary">Cancel</button>
                <button type="submit" id="btn-save" class="btn-primary">Save Post</button>
            </div>
        </form>
    </div>
</div>

<style>
    * { box-sizing: border-box; }

    .blog-manager { max-width: 1400px; margin: 0 auto; padding: 30px 20px; }
    .blog-header { margin-bottom: 30px; }
    .blog-header h1 { margin: 0 0 5px 0; font-size: 2rem; color: var(--mfa-dark); }
    .blog-header p { margin: 0; color: var(--mfa-text-light); font-size: 1rem; }

    .blog-toolbar { display: flex; gap: 15px; margin-bottom: 30px; align-items: center; }
    .btn-primary { background: var(--mfa-blue); color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.95rem; transition: background 0.2s; }
    .btn-primary:hover { background: #0056b3; }
    .btn-secondary { background: #e9ecef; color: #333; border: 1px solid #dee2e6; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.95rem; }
    .btn-secondary:hover { background: #dee2e6; }
    .filter-select { padding: 10px 15px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.95rem; cursor: pointer; }

    .posts-list { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .loader { text-align: center; padding: 60px 20px; }
    .spinner { display: inline-block; width: 40px; height: 40px; border: 4px solid #f0f0f0; border-top-color: var(--mfa-blue); border-radius: 50%; animation: spin 0.8s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .posts-table { width: 100%; border-collapse: collapse; }
    .posts-table th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #dee2e6; }
    .posts-table td { padding: 15px; border-bottom: 1px solid #dee2e6; }
    .posts-table tr:hover { background: #f8f9fa; }

    .post-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; background: #e9ecef; }
    .post-title { font-weight: 600; }
    .post-status { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 600; text-transform: capitalize; }
    .status-published { background: #d4edda; color: #155724; }
    .status-draft { background: #fff3cd; color: #856404; }
    .status-archived { background: #e2e3e5; color: #495057; }

    .post-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .btn-sm { padding: 6px 12px; font-size: 0.85rem; border: none; border-radius: 4px; cursor: pointer; transition: 0.2s; }
    .btn-edit { background: var(--mfa-blue); color: white; }
    .btn-edit:hover { background: #0056b3; }
    .btn-delete { background: #dc3545; color: white; }
    .btn-delete:hover { background: #c82333; }
    .btn-toggle { background: #6c757d; color: white; }
    .btn-toggle:hover { background: #5a6268; }

    /* Modal */
    .modal-backdrop { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 9999; padding: 20px; }
    .modal-backdrop.active { display: flex; }
    .modal-window { background: white; border-radius: 8px; width: 100%; max-width: 90vw; max-height: 90vh; display: flex; flex-direction: column; }
    .modal-header { padding: 20px 25px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
    .modal-header h2 { margin: 0; font-size: 1.5rem; }
    .btn-close { background: none; border: none; font-size: 2rem; cursor: pointer; color: #6c757d; }
    .btn-close:hover { color: #000; }
    .modal-body { flex: 1; overflow-y: auto; padding: 25px; }
    .modal-footer { padding: 20px 25px; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; background: #f8f9fa; }

    .editor-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
    .editor-main { display: flex; flex-direction: column; gap: 20px; }
    .editor-sidebar { display: flex; flex-direction: column; gap: 20px; }

    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-group label { font-weight: 600; }
    .form-group input[type="text"], .form-group select { padding: 10px; border: 1px solid #dee2e6; border-radius: 4px; font-size: 0.95rem; }
    .form-group input[type="text"]:focus, .form-group select:focus { outline: none; border-color: var(--mfa-blue); box-shadow: 0 0 0 3px rgba(0,123,255,0.15); }

    #editor-quill { height: 400px; background: white; border-radius: 4px; }

    .sidebar-panel { background: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #dee2e6; }
    .sidebar-panel h3 { margin: 0 0 15px 0; font-size: 1rem; }
    .panel-hint { margin: 0 0 15px 0; font-size: 0.85rem; color: #6c757d; line-height: 1.4; }

    .media-panel { display: flex; flex-direction: column; flex: 1; min-height: 400px; }
    .filepond--root { margin-bottom: 15px; }
    .filepond--panel-root { background: white; border: 2px dashed #dee2e6; }

    .gallery-grid { flex: 1; border: 1px solid #dee2e6; border-radius: 4px; padding: 10px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; align-content: start; overflow-y: auto; background: white; }
    .gallery-empty { grid-column: 1 / -1; text-align: center; color: #6c757d; padding: 30px 10px; font-size: 0.9rem; }

    .media-item { position: relative; aspect-ratio: 1; border: 1px solid #dee2e6; border-radius: 4px; overflow: hidden; cursor: grab; background: #e9ecef; }
    .media-item img, .media-item video { width: 100%; height: 100%; object-fit: cover; }
    .media-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: space-around; opacity: 0; transition: opacity 0.2s; }
    .media-item:hover .media-overlay { opacity: 1; }
    .media-btn { width: 36px; height: 36px; border-radius: 50%; background: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; transition: background 0.2s; }
    .media-btn:hover { background: #f0f0f0; }
    .featured-badge { position: absolute; top: 6px; left: 6px; background: var(--mfa-blue); color: white; padding: 3px 8px; border-radius: 3px; font-size: 0.7rem; font-weight: 600; }

    .save-status { font-weight: 600; font-size: 0.95rem; }
    .save-status.success { color: #28a745; }
    .save-status.error { color: #dc3545; }

    @media (max-width: 1024px) {
        .editor-layout { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .blog-toolbar { flex-direction: column; }
        .gallery-grid { grid-template-columns: repeat(3, 1fr); }
        .posts-table { font-size: 0.9rem; }
    }
</style>

<script>
const CONFIG = {
    mediaUrl: '<?php echo $media_base_url; ?>',
    apiUrl: './blog_ajax.php'
};

let quill;
let filepond;
let mediaGallery = [];
let currentPostId = 0;

document.addEventListener('DOMContentLoaded', () => {
    initializeQuill();
    initializeFilePond();
    loadPosts();
    setupEventListeners();
});

function initializeQuill() {
    quill = new Quill('#editor-quill', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });
}

function initializeFilePond() {
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginFileValidateType,
        FilePondPluginFileValidateSize
    );

    filepond = FilePond.create(document.getElementById('input-filepond'), {
        allowMultiple: true,
        maxFiles: 10,
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/gif', 'video/mp4', 'video/webm'],
        maxFileSize: '50MB',
        labelIdle: 'Drag files or <span class="filepond--label-action">browse</span>',
        server: {
            process: CONFIG.apiUrl + '?action=upload_media',
        },
        onaddfile: (err, file) => {
            if (err) console.error('Upload error:', err);
        },
        onprocessfile: (err, file) => {
            if (!err && file.serverId) {
                addToGallery(file.serverId, 'image');
            }
        },
        onremovefile: (file) => {
            if (file.serverId) {
                mediaGallery = mediaGallery.filter(m => m.name !== file.serverId);
                updateGalleryPreview();
            }
        }
    });
}

function setupEventListeners() {
    document.getElementById('btn-new-post').addEventListener('click', openNewPost);
    document.getElementById('btn-close-modal').addEventListener('click', closeModal);
    document.getElementById('btn-cancel').addEventListener('click', closeModal);
    document.getElementById('form-post').addEventListener('submit', savePost);
    document.getElementById('filter-status').addEventListener('change', (e) => filterPosts(e.target.value));
}

function openNewPost() {
    currentPostId = 0;
    document.getElementById('modal-title').textContent = 'New Post';
    document.getElementById('form-post').reset();
    document.getElementById('input-title').value = '';
    document.getElementById('input-status').value = 'draft';
    quill.setContents([]);
    filepond.removeFiles();
    mediaGallery = [];
    updateGalleryPreview();
    document.getElementById('modal-editor').classList.add('active');
}

function closeModal() {
    if (confirm('Close without saving?')) {
        document.getElementById('modal-editor').classList.remove('active');
    }
}

function addToGallery(filename, type) {
    if (!mediaGallery.find(m => m.name === filename)) {
        mediaGallery.push({
            name: filename,
            type: type,
            featured: mediaGallery.length === 0
        });
        updateGalleryPreview();
    }
}

function updateGalleryPreview() {
    const gallery = document.getElementById('gallery-preview');
    
    if (mediaGallery.length === 0) {
        gallery.innerHTML = '<p class="gallery-empty">No media uploaded</p>';
        return;
    }

    let html = '';
    mediaGallery.forEach((item, idx) => {
        const url = CONFIG.mediaUrl + item.name;
        const isFeatured = item.featured;
        
        html += `
            <div class="media-item" draggable="true" data-idx="${idx}">
                ${item.type === 'video' ? `<video src="${url}"></video>` : `<img src="${url}" alt="Media">`}
                ${isFeatured ? '<div class="featured-badge">Featured</div>' : ''}
                <div class="media-overlay">
                    <button type="button" class="media-btn" onclick="toggleFeatured(${idx})" title="Set Featured">
                        <i class="fas fa-star"></i>
                    </button>
                    <button type="button" class="media-btn" onclick="removeFromGallery(${idx})" title="Remove">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });

    gallery.innerHTML = html;
    setupDragDrop();
}

function toggleFeatured(idx) {
    mediaGallery.forEach((item, i) => {
        item.featured = (i === idx);
    });
    updateGalleryPreview();
}

function removeFromGallery(idx) {
    mediaGallery.splice(idx, 1);
    if (mediaGallery.length > 0 && !mediaGallery.some(m => m.featured)) {
        mediaGallery[0].featured = true;
    }
    updateGalleryPreview();
}

function setupDragDrop() {
    const items = document.querySelectorAll('.media-item');
    items.forEach(item => {
        item.addEventListener('dragstart', (e) => {
            e.dataTransfer.effectAllowed = 'move';
            item.style.opacity = '0.5';
        });
        item.addEventListener('dragend', () => item.style.opacity = '1');
        item.addEventListener('dragover', (e) => e.preventDefault());
        item.addEventListener('drop', (e) => {
            e.preventDefault();
            const from = parseInt(e.dataTransfer.getData('text/html') || item.dataset.idx);
            const to = parseInt(item.dataset.idx);
            if (from !== to) {
                [mediaGallery[from], mediaGallery[to]] = [mediaGallery[to], mediaGallery[from]];
                updateGalleryPreview();
            }
        });
    });
}

async function savePost(e) {
    e.preventDefault();
    const status = document.getElementById('save-status');
    status.textContent = 'Saving...';

    const formData = new FormData();
    formData.append('action', 'save_post');
    formData.append('post_id', currentPostId);
    formData.append('title', document.getElementById('input-title').value);
    formData.append('status', document.getElementById('input-status').value);
    formData.append('content', JSON.stringify(quill.getContents()));

    mediaGallery.forEach(item => {
        formData.append('media_files[]', item.name);
        if (item.featured) formData.append('featured_image', item.name);
    });

    try {
        const response = await fetch(CONFIG.apiUrl, { method: 'POST', body: formData });
        const result = await response.json();

        if (result.status === 'success') {
            status.classList.add('success');
            status.textContent = 'Saved!';
            currentPostId = result.post_id;
            setTimeout(() => {
                document.getElementById('modal-editor').classList.remove('active');
                loadPosts();
            }, 1500);
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        status.classList.add('error');
        status.textContent = 'Error: ' + error.message;
    }
}

async function loadPosts() {
    const container = document.getElementById('posts-container');
    
    try {
        const response = await fetch(CONFIG.apiUrl + '?action=fetch_posts');
        const result = await response.json();

        if (result.status !== 'success') throw new Error(result.message);

        if (!result.posts || result.posts.length === 0) {
            container.innerHTML = '<p style="text-align: center; padding: 40px; color: #6c757d;">No posts yet. Create one to get started!</p>';
            return;
        }

        let html = `
            <table class="posts-table">
                <thead>
                    <tr>
                        <th colspan="2">Title</th>
                        <th>Status</th>
                        <th>Author</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;

        result.posts.forEach(post => {
            const img = post.featured_image ? CONFIG.mediaUrl + post.featured_image : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="50" height="50"%3E%3Crect fill="%23e9ecef" width="50" height="50"/%3E%3C/svg%3E';
            const date = new Date(post.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            const statusClass = `status-${post.status}`;

            html += `
                <tr data-status="${post.status}">
                    <td><img src="${img}" alt="" class="post-img"></td>
                    <td class="post-title">${post.title}</td>
                    <td><span class="post-status ${statusClass}">${post.status}</span></td>
                    <td>${post.author_name}</td>
                    <td>${date}</td>
                    <td class="post-actions">
                        <button class="btn-sm btn-edit" onclick="editPost(${post.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn-sm btn-toggle" onclick="changeStatus(${post.id}, '${post.status}')"><i class="fas fa-exchange-alt"></i></button>
                        <button class="btn-sm btn-delete" onclick="deletePost(${post.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        container.innerHTML = html;

    } catch (error) {
        container.innerHTML = `<p style="color: #dc3545; padding: 20px;">Error: ${error.message}</p>`;
    }
}

async function editPost(id) {
    try {
        const response = await fetch(CONFIG.apiUrl + '?action=get_post_details&id=' + id);
        const result = await response.json();

        if (result.status !== 'success') throw new Error(result.message);

        const post = result.post;
        const media = result.media || [];

        currentPostId = post.id;
        document.getElementById('modal-title').textContent = 'Edit Post';
        document.getElementById('input-title').value = post.title;
        document.getElementById('input-status').value = post.status;
        quill.setContents(JSON.parse(post.content));

        mediaGallery = media.map((m, idx) => ({
            name: m.file_path,
            type: m.media_type,
            featured: m.is_featured == 1
        }));

        filepond.removeFiles();
        updateGalleryPreview();

        document.getElementById('modal-editor').classList.add('active');

    } catch (error) {
        alert('Error loading post: ' + error.message);
    }
}

async function changeStatus(id, currentStatus) {
    const statuses = ['draft', 'published', 'archived'];
    const nextStatus = statuses[(statuses.indexOf(currentStatus) + 1) % statuses.length];

    if (!confirm(`Change status to "${nextStatus}"?`)) return;

    try {
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('id', id);
        formData.append('status', nextStatus);

        const response = await fetch(CONFIG.apiUrl, { method: 'POST', body: formData });
        const result = await response.json();

        if (result.status === 'success') {
            loadPosts();
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function deletePost(id) {
    if (!confirm('PERMANENTLY delete this post?')) return;

    try {
        const formData = new FormData();
        formData.append('action', 'delete_post');
        formData.append('id', id);

        const response = await fetch(CONFIG.apiUrl, { method: 'POST', body: formData });
        const result = await response.json();

        if (result.status === 'success') {
            loadPosts();
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

function filterPosts(status) {
    document.querySelectorAll('.posts-table tbody tr').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
    });
}
</script>

<?php require_once __DIR__ . '/ic/afooter.php'; ?>