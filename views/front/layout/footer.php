</main>

<footer class="ma-footer">
    <div class="container footer-inner">
        <div class="brand">
            <span class="logo-dot"></span>
            <span class="brand-name">MindArena</span>
        </div>
        <div class="meta">
            <span>© 2025 MindArena</span>
            <span class="divider">•</span>
            <span>Built for gamers</span>
        </div>
    </div>
</footer>

<style>
    .ma-footer {
        background: linear-gradient(180deg, rgba(8,22,36,0.95) 0%, rgba(10,8,21,0.98) 100%);
        border-top: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 -8px 24px rgba(0,0,0,0.35);
        color: #cbd5f5;
        padding: 22px 0;
        margin-top: 40px;
    }
    .ma-footer .footer-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 14px;
    }
    .ma-footer .brand {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 800;
        letter-spacing: 0.3px;
        color: #f5e7ff;
    }
    .ma-footer .logo-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: radial-gradient(circle, #ff4df0 0%, #7b2ff7 70%);
        box-shadow: 0 0 10px rgba(255,77,240,0.5);
    }
    .ma-footer .meta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #9ca3af;
    }
    .ma-footer .divider { opacity: 0.6; }
    @media (max-width: 768px) {
        .ma-footer .footer-inner {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

</body>
</html>

