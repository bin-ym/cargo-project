<?php
require_once __DIR__ . '/../../backend/config/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

require_once __DIR__ . '/../layout/header_customer.php';
?>

<div class="customer-layout">
    <?php include __DIR__ . '/../layout/navbar_customer.php'; ?>

    <main class="main-content" style="padding: 30px 5%; max-width: 1200px; margin: 0 auto;">
        <header class="topbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h2>Notifications</h2>
            <button class="btn btn-secondary" onclick="markAllAsRead()">
                Mark all as read
            </button>
        </header>

        <div class="notifications-list" id="notificationsList">
            <!-- Skeleton loader -->
            <div class="notification-skeleton"></div>
            <div class="notification-skeleton"></div>
            <div class="notification-skeleton"></div>
        </div>
    </main>
</div>

<style>
.notification-item {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 14px;
    display: flex;
    gap: 15px;
    cursor: pointer;
    transition: all .2s;
}
.notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,.08);
}
.notification-item.unread {
    border-left: 4px solid #3b82f6;
    background: #eff6ff;
}

.icon-box {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.icon-box.success { background:#dcfce7; color:#16a34a; }
.icon-box.warning { background:#fef3c7; color:#d97706; }
.icon-box.error   { background:#fee2e2; color:#dc2626; }
.icon-box.info    { background:#e0f2fe; color:#0284c7; }

.title {
    font-weight: 600;
    color: #0f172a;
}
.message {
    color: #475569;
    margin: 6px 0;
}
.time {
    font-size: 13px;
    color: #94a3b8;
}

/* Skeleton */
.notification-skeleton {
    height: 86px;
    background: linear-gradient(90deg,#f1f5f9 25%,#e5e7eb 37%,#f1f5f9 63%);
    background-size: 400% 100%;
    animation: skeleton 1.4s ease infinite;
    border-radius: 12px;
    margin-bottom: 14px;
}
@keyframes skeleton {
    0% { background-position: 100% 0 }
    100% { background-position: -100% 0 }
}
</style>

<script>
const API = '/cargo-project/backend/api/customer/get_notifications.php';
const MARK_READ = '/cargo-project/backend/api/customer/mark_notification_read.php';
const MARK_ALL = '/cargo-project/backend/api/customer/notifications.php';

async function fetchNotifications() {
    const list = document.getElementById('notificationsList');
    try {
        const res = await fetch(API);
        const json = await res.json();
        list.innerHTML = '';

        if (!json.success || json.data.length === 0) {
            list.innerHTML = `<div style="text-align:center;color:#64748b;padding:40px;">No notifications</div>`;
            updateNavbarCount(0);
            return;
        }

        let unreadCount = 0;

        json.data.forEach(n => {
            if (n.is_read == 0) unreadCount++;

            list.innerHTML += `
                <div class="notification-item ${n.is_read == 0 ? 'unread' : ''}"
                     onclick="openNotification(${n.id}, ${n.related_request_id || 'null'})">

                    <div class="icon-box ${n.type || 'info'}">
                        <i data-feather="${iconFor(n.type)}"></i>
                    </div>

                    <div>
                        <div class="title">${n.title}</div>
                        <div class="message">${n.message}</div>
                        <div class="time">${timeAgo(n.created_at)}</div>
                    </div>
                </div>
            `;
        });

        feather.replace();
        updateNavbarCount(unreadCount);

    } catch (e) {
        list.innerHTML = `<div style="text-align:center;color:red;">Failed to load notifications</div>`;
    }
}

function iconFor(type) {
    return {
        success:'check-circle',
        warning:'alert-triangle',
        error:'x-circle',
        info:'info'
    }[type] || 'info';
}

/* Click notification */
async function openNotification(id, requestId) {
    await fetch(MARK_READ, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({id})
    });

    if (requestId) {
        window.location.href = `track_shipment.php?id=${requestId}`;
    } else {
        fetchNotifications();
    }
}

/* Mark all */
async function markAllAsRead() {
    await fetch(MARK_ALL, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({action:'mark_read'})
    });
    fetchNotifications();
}

/* Navbar badge */
function updateNavbarCount(count) {
    const badge = document.getElementById('notificationCount');
    if (!badge) return;
    badge.style.display = count > 0 ? 'inline-block' : 'none';
    badge.innerText = count;
}

/* Time ago */
function timeAgo(dateStr) {
    const sec = Math.floor((new Date() - new Date(dateStr)) / 1000);
    if (sec < 60) return 'Just now';
    if (sec < 3600) return Math.floor(sec/60) + ' minutes ago';
    if (sec < 86400) return Math.floor(sec/3600) + ' hours ago';
    return Math.floor(sec/86400) + ' days ago';
}

fetchNotifications();
</script>

<?php require_once __DIR__ . '/../layout/footer_customer.php'; ?>