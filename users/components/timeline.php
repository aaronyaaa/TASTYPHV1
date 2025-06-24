<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Timeline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .reaction-bar:hover .reaction-options {
            display: flex !important;
        }

        .reaction-options {
            display: none;
            position: absolute;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 20px;
            padding: 5px;
            gap: 5px;
        }

        .reaction-options span {
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 10px;
        }

        .reaction-options span:hover {
            background-color: #f0f0f0;
        }

        .fb-timeline-bg {
            background: #f0f2f5;
            min-height: 100vh;
            padding: 32px 0;
        }
        .fb-post.card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin-bottom: 32px;
        }
        .fb-post .post-content img, .fb-post .post-content video {
            border-radius: 12px;
            max-width: 100%;
            margin-bottom: 8px;
            max-height: 400px;
            object-fit: cover;
        }
        .fb-post .post-content video { width: 100%; }
        .fb-post .action-bar button {
            background: none;
            border: none;
            color: #65676b;
            font-weight: 500;
            font-size: 1rem;
            padding: 6px 18px;
            border-radius: 8px;
            transition: background 0.15s;
        }
        .fb-post .action-bar button:hover {
            background: #f0f2f5;
            color: #1877f2;
        }
        .fb-post .avatar {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 12px;
        }
        .fb-post .user-name {
            font-weight: 600;
            color: #050505;
        }
        .fb-post .post-time {
            color: #65676b;
            font-size: 0.95em;
        }
        .fb-reaction-bar {
            display: flex;
            gap: 8px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            padding: 6px 12px;
            position: absolute;
            bottom: 38px;
            left: 0;
            z-index: 10;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s;
        }
        .fb-reaction-bar.show {
            opacity: 1;
            pointer-events: auto;
        }
        .fb-reaction-bar .reaction {
            font-size: 1.7rem;
            cursor: pointer;
            transition: transform 0.1s;
        }
        .fb-reaction-bar .reaction:hover {
            transform: scale(1.25);
        }
        .action-bar {
            position: relative;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container my-4">
        <button class="btn btn-light w-100 text-start mb-3" data-bs-toggle="modal" data-bs-target="#postModal">
            What's on your mind?
        </button>

        <!-- Timeline container -->
        <div id="timeline"></div>
    </div>

    <!-- Post Modal -->
    <div class="modal fade" id="postModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="postForm" action="../backend/post_create_ajax.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Post</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control mb-3" name="content" placeholder="What's on your mind?" required></textarea>
                        <input type="file" name="media_files[]" class="form-control mb-3" multiple>
                        <select name="audience" class="form-select mb-3" required>
                            <option value="public">Public</option>
                            <option value="friends">Friends</option>
                            <option value="only_me">Only Me</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Post</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script>
    function loadTimeline() {
        $.get('../backend/load_posts.php', function(data) {
            $('#timeline').html(data);
        });
    }

    $(document).ready(function () {
        loadTimeline();

        $('#postForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            $.ajax({
                url: '../backend/post_create_ajax.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
 success: function(response) {
    const modalEl = document.getElementById('postModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modalInstance.hide();

    // Delay a little to ensure modal fades out first
    setTimeout(() => {
        $('.modal-backdrop').remove(); // ✅ Removes the dark backdrop
        $('body').removeClass('modal-open'); // ✅ Fixes body scroll lock
        $('#postModal').removeAttr('aria-hidden'); // ✅ Fix accessibility layer
        $('#postForm')[0].reset();
        $('#timeline').prepend(response);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }, 200);
}

            });
        });

        document.querySelectorAll('.like-btn').forEach(btn => {
            const bar = btn.querySelector('.fb-reaction-bar');
            let hideTimeout;
            btn.addEventListener('mouseenter', () => {
                clearTimeout(hideTimeout);
                bar.classList.add('show');
            });
            btn.addEventListener('mouseleave', () => {
                hideTimeout = setTimeout(() => {
                    bar.classList.remove('show');
                }, 400); // 400ms delay
            });
            bar.addEventListener('mouseenter', () => {
                clearTimeout(hideTimeout);
                bar.classList.add('show');
            });
            bar.addEventListener('mouseleave', () => {
                hideTimeout = setTimeout(() => {
                    bar.classList.remove('show');
                }, 400);
            });
            bar.querySelectorAll('.reaction').forEach(react => {
                react.addEventListener('click', () => {
                    bar.classList.remove('show');
                    // TODO: send reaction to backend, update UI, etc.
                });
            });
        });
    });
</script>

</body>

</html>