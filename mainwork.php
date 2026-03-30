<?php

session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_name  = htmlspecialchars($_SESSION['user_name']);
$user_email = htmlspecialchars($_SESSION['user_email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Planner - Dashboard</title>
    <link rel="shortcut icon" href="assets/logo.png" type="image/x-icon" />
    <link rel="stylesheet" href="css/style.css" />
    <style>
        :root {
            --primary:    #e34432;
            --primary-dk: #c0392b;
            --bg:         #f8f9fa;
            --white:      #ffffff;
            --border:     #e2e8f0;
            --text:       #1a202c;
            --muted:      #718096;
            --success-bg: #f0fff4;
            --success-cl: #276749;
            --warn-bg:    #fffaf0;
            --warn-cl:    #975a16;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* navbar */
        nav {
            position: sticky;
            top: 0;
            z-index: 900;
            background: var(--white);
            padding: 0 45px;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-left img { height: 36px; width: 40px; }

        .nav-left span {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff6f4;
            border: 1px solid #fcd5cf;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-dk);
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        .logout-btn {
            padding: 8px 18px;
            background: transparent;
            border: 1.5px solid var(--primary);
            color: var(--primary);
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: .2s;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: var(--primary);
            color: white;
        }

        /* main layout */
        .dashboard {
            max-width: 780px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .dashboard-header {
            margin-bottom: 32px;
        }

        .dashboard-header h1 {
            font-size: 28px;
            font-weight: 700;
        }

        .dashboard-header p {
            color: var(--muted);
            margin-top: 4px;
            font-size: 15px;
        }

        /* cards*/
        .card {
            background: var(--white);
            padding: 22px 24px;
            border-radius: 14px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
            margin-bottom: 18px;
        }

        .card h4 {
            margin-bottom: 16px;
            font-size: 15px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* input group */
        .input-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .input-group input {
            flex: 1;
            min-width: 140px;
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-size: 15px;
            outline: none;
            transition: border-color .2s;
            font-family: inherit;
        }

        .input-group input:focus {
            border-color: var(--primary);
        }

        #add-task-btn {
            padding: 11px 22px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 9px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            white-space: nowrap;
            transition: .2s;
        }

        #add-task-btn:hover { background: var(--primary-dk); }
        #add-task-btn:disabled { opacity: 0.6; cursor: not-allowed; }

        /*progress */
        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .progress-bar-bg {
            background: #edf2f7;
            height: 8px;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), #ff7043);
            width: 0%;
            transition: width 0.5s cubic-bezier(.4,0,.2,1);
            border-radius: 10px;
        }

        /*stats row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-box {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 18px;
            text-align: center;
        }

        .stat-num {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
        }

        .stat-label {
            font-size: 13px;
            color: var(--muted);
            margin-top: 2px;
        }

        /*tasks items */
        .task-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 4px 0;
        }

        .task-item + .task-item {
            border-top: 1px solid var(--border);
            margin-top: 14px;
            padding-top: 14px;
        }

        .task-left {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            flex: 1;
        }

        .task-left input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary);
            margin-top: 3px;
            flex-shrink: 0;
        }

        .task-title {
            font-size: 15px;
            font-weight: 600;
            transition: .2s;
        }

        .task-title.done {
            text-decoration: line-through;
            color: #cbd5e0;
        }

        .task-subject {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        .task-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-pending   { background: var(--warn-bg);    color: var(--warn-cl); }
        .badge-completed { background: var(--success-bg); color: var(--success-cl); }

        .delete-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #e53e3e;
            padding: 4px 6px;
            border-radius: 6px;
            transition: background .15s;
        }

        .delete-btn:hover { background: #fff5f5; }

        /*empty/loading*/
        .empty-msg {
            text-align: center;
            color: var(--muted);
            padding: 48px 20px;
            font-size: 15px;
        }

        .loading-msg {
            text-align: center;
            color: var(--muted);
            padding: 24px;
            font-size: 14px;
        }

        /*notification*/
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 13px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            z-index: 9999;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { background: #2d7a4f; }
        .toast.error   { background: #c0392b; }

        .hidden { display: none !important; }
    </style>
</head>
<body>

    <nav>
        <div class="nav-left">
            <a href="index.html"><img src="assets/logo.png" alt="logo"></a>
            <span>Smart Planner</span>
        </div>
        <div class="nav-right">
            <div class="user-badge">
                <div class="user-avatar"><?= strtoupper(substr($user_name, 0, 1)) ?></div>
                <?= $user_name ?>
            </div>
            <a href="php/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div id="toast" class="toast"></div>

    <div class="dashboard">

        <!-- header -->
        <div class="dashboard-header">
            <h1>Welcome back, <?= $user_name ?>! 👋</h1>
            <p>Here's your study progress for today.</p>
        </div>

        <!-- stats -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-num" id="stat-total">—</div>
                <div class="stat-label">Total Tasks</div>
            </div>
            <div class="stat-box">
                <div class="stat-num" id="stat-done">—</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-box">
                <div class="stat-num" id="stat-pending">—</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <!-- progress -->
        <div class="card" id="progress-card" style="display:none;">
            <div class="progress-header">
                <span>Overall Progress</span>
                <span id="progress-text">0 of 0 completed</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" id="progress-fill"></div>
            </div>
        </div>

        <!-- add task -->
        <div class="card">
            <h4>Add New Task</h4>
            <div class="input-group">
                <input type="text" id="subject-input" placeholder="Subject (e.g. Maths)">
                <input type="text" id="task-input"    placeholder="Task (e.g. Chapter 3 exercises)">
                <button id="add-task-btn">+ Add Task</button>
            </div>
        </div>

        <!-- task list -->
        <div class="card" id="task-list-card">
            <h4>Your Tasks</h4>
            <div id="loading-msg" class="loading-msg">Loading your tasks...</div>
            <div id="task-list"></div>
            <div id="empty-msg" class="empty-msg hidden">
                No tasks yet. Start by adding one above! 📝
            </div>
        </div>

    </div>

    <script>
        //helper to show toast notifications
        function showToast(msg, type = 'success') {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = `toast ${type} show`;
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        async function api(action, body = {}) {
            const formData = new FormData();
            formData.append('action', action);
            for (const [k, v] of Object.entries(body)) formData.append(k, v);

            const res  = await fetch(`php/tasks.php?action=${action}`, {
                method: 'POST',
                body: formData
            });
            return res.json();
        }

        // state
        let tasks = [];

        function updateStats() {
            const total   = tasks.length;
            const done    = tasks.filter(t => t.completed).length;
            const pending = total - done;
            const pct     = total ? Math.round((done / total) * 100) : 0;

            document.getElementById('stat-total').textContent   = total;
            document.getElementById('stat-done').textContent    = done;
            document.getElementById('stat-pending').textContent = pending;
            document.getElementById('progress-text').textContent =
                `${done} of ${total} tasks completed (${pct}%)`;
            document.getElementById('progress-fill').style.width = pct + '%';

            document.getElementById('progress-card').style.display = total ? 'block' : 'none';
        }

        function renderTasks() {
            const list     = document.getElementById('task-list');
            const emptyMsg = document.getElementById('empty-msg');

            if (tasks.length === 0) {
                list.innerHTML = '';
                emptyMsg.classList.remove('hidden');
            } else {
                emptyMsg.classList.add('hidden');
                list.innerHTML = tasks.map(t => `
                    <div class="task-item" id="task-${t.id}">
                        <div class="task-left">
                            <input type="checkbox"
                                ${t.completed ? 'checked' : ''}
                                onchange="toggleTask(${t.id})">
                            <div>
                                <div class="task-title ${t.completed ? 'done' : ''}">${escHtml(t.title)}</div>
                                <div class="task-subject">${escHtml(t.subject)}</div>
                            </div>
                        </div>
                        <div class="task-right">
                            <span class="badge ${t.completed ? 'badge-completed' : 'badge-pending'}">
                                ${t.completed ? 'Completed' : 'Pending'}
                            </span>
                            <button class="delete-btn" onclick="deleteTask(${t.id})" title="Delete task">🗑️</button>
                        </div>
                    </div>
                `).join('');
            }

            updateStats();
        }

        function escHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        //load tasks on page load
        async function loadTasks() {
            try {
                const data = await fetch('php/tasks.php?action=list').then(r => r.json());
                document.getElementById('loading-msg').style.display = 'none';

                if (data.success) {
                    tasks = data.tasks;
                    renderTasks();
                } else {
                    showToast(data.message || 'Failed to load tasks.', 'error');
                }
            } catch (e) {
                document.getElementById('loading-msg').textContent =
                    '⚠️ Could not connect. Is XAMPP running?';
            }
        }

        //add task
        document.getElementById('add-task-btn').addEventListener('click', async () => {
            const subjectEl = document.getElementById('subject-input');
            const taskEl    = document.getElementById('task-input');
            const btn       = document.getElementById('add-task-btn');

            const subject = subjectEl.value.trim();
            const title   = taskEl.value.trim();

            if (!subject || !title) {
                showToast('Please fill in both subject and task fields.', 'error');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Adding...';

            try {
                const data = await api('add', { subject, title });

                if (data.success) {
                    tasks.unshift(data.task);
                    renderTasks();
                    subjectEl.value = '';
                    taskEl.value    = '';
                    showToast('Task added!', 'success');
                } else {
                    showToast(data.message, 'error');
                }
            } catch (e) {
                showToast('Failed to add task.', 'error');
            }

            btn.disabled = false;
            btn.textContent = '+ Add Task';
        });

        // toggle task
        async function toggleTask(id) {
            try {
                const data = await api('toggle', { id });
                if (data.success) {
                    tasks = tasks.map(t => t.id === id ? { ...t, completed: !t.completed } : t);
                    renderTasks();
                } else {
                    showToast(data.message, 'error');
                    loadTasks(); // Refresh to sync state
                }
            } catch (e) {
                showToast('Failed to update task.', 'error');
            }
        }

        // delete task
        async function deleteTask(id) {
            if (!confirm('Delete this task?')) return;

            try {
                const data = await api('delete', { id });
                if (data.success) {
                    tasks = tasks.filter(t => t.id !== id);
                    renderTasks();
                    showToast('Task deleted.', 'success');
                } else {
                    showToast(data.message, 'error');
                }
            } catch (e) {
                showToast('Failed to delete task.', 'error');
            }
        }

        // allow enter key to add task
        ['subject-input', 'task-input'].forEach(id => {
            document.getElementById(id).addEventListener('keypress', (e) => {
                if (e.key === 'Enter') document.getElementById('add-task-btn').click();
            });
        });

        // initialize
        loadTasks();
    </script>

</body>
</html>
