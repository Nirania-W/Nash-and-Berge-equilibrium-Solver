<!DOCTYPE html>
<html lang="th">

<head>
    <title>Game Theory Solver — Nash & Berge Equilibrium</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="โปรแกรมคำนวณจุดสมดุลแนช (Nash) และเบิร์จ (Berge) ด้วยวิธีกำหนดการไม่เชิงเส้น สำหรับ Senior Project สาขาคณิตศาสตร์ มหาวิทยาลัยสงขลานครินทร์">
    <link rel="stylesheet" href="dec.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dec.css">
</head>

<body>

    <!-- Geometric Grid Background -->
    <div class="hero-bg-grid"></div>

    <!-- Enhanced Decorative Shapes -->
    <div class="bg-decoration bg-decoration-enhanced">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Floating Mathematical Symbols -->
    <div class="floating-symbols">
        <span class="symbol">∑</span>
        <span class="symbol">∫</span>
        <span class="symbol">∂</span>
        <span class="symbol">∞</span>
        <span class="symbol">π</span>
        <span class="symbol">Δ</span>
        <span class="symbol">λ</span>
        <span class="symbol">∇</span>
    </div>

    <!-- Animated Rings -->
    <div class="bg-ring"></div>
    <div class="bg-ring"></div>
    <div class="bg-ring"></div>

    <!-- Animated Whale -->
    <div class="whale-container">
        <img class="whale-svg" src="image\giphy.gif" alt="Swimming Whale">
    </div>

    <div class="main-container hero-section">
        <div class="hero-card-3d text-center w-100" style="max-width: 820px;">

            <!-- Badge -->
            <div class="mb-3">
                <span class="hero-badge-enhanced">
                    Senior Project 2026
                </span>
            </div>

            <!-- Title -->
            <h1 class="project-title-enhanced">
                Game Theory<br>Equilibrium Solver
            </h1>

            <!-- Subtitle -->
            <h2 class="h5 mb-2" style="color: var(--text-secondary); font-weight: 500; font-size: 1.1rem;">
                Nash and Berge Equilibrium
            </h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 0;">
                Using Nonlinear Programming Method
            </p>

            <!-- Animated Divider -->
            <div class="hero-divider-animated"></div>

            <!-- Description -->
            <p class="hero-description">
                โปรแกรมสนับสนุนการตัดสินใจทางคณิตศาสตร์สำหรับ<strong>ทฤษฎีเกม</strong><br>
                ด้วยวิธีการแก้ปัญหา<strong>กำหนดการไม่เชิงเส้น</strong> (D.C. Optimization)
            </p>

            <!-- CTA Buttons -->
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center mb-4">
                <a href="cal_nash.php" class="btn-cta-primary px-5">
                    <span>Nash Equilibrium</span>
                    <span class="btn-cta-label">จุดสมดุลแนช</span>
                </a>
                <a href="cal_berge.php" class="btn-cta-secondary px-5">
                    <span>Berge Equilibrium</span>
                    <span class="btn-cta-label">จุดสมดุลเบิร์จ</span>
                </a>
            </div>

            <!-- Feature Cards with 3D depth -->
            <div class="features-grid" style="margin-top: 2.5rem;">
                <div class="feature-card-3d">
                    <div class="feature-card-title">รองรับ Matrix ขนาดสูงสุด</div>
                    <div class="feature-card-desc">50×50 Matrix</div>
                </div>
                <div class="feature-card-3d">
                    <div class="feature-card-title">D.C. Optimization</div>
                    <div class="feature-card-desc">Nonlinear Programming</div>
                </div>
                <div class="feature-card-3d">
                    <div class="feature-card-title">วาง Excel ได้ทันที</div>
                    <div class="feature-card-desc">Copy & Paste</div>
                </div>
            </div>

            <!-- Info Ribbon -->
            <div class="info-ribbon">
                <div class="info-ribbon-item">
                    <span class="info-ribbon-value">2</span>
                    <span class="info-ribbon-label">Equilibrium Types</span>
                </div>
                <div class="info-ribbon-item">
                    <span class="info-ribbon-value">50×50</span>
                    <span class="info-ribbon-label">Matrix Support</span>
                </div>
                <div class="info-ribbon-item">
                    <span class="info-ribbon-value">SciPy</span>
                    <span class="info-ribbon-label">Powered By</span>
                </div>
            </div>

            <!-- University Info -->
            <div class="university-bar text-center">
                <small>
                    สาขาวิชาคณิตศาสตร์และวิทยาการคอมพิวเตอร์<br>
                    คณะวิทยาศาสตร์และเทคโนโลยี มหาวิทยาลัยสงขลานครินทร์ วิทยาเขตปัตตานี
                </small>
            </div>

        </div>
    </div>

    <footer class="academic-footer">
        &copy; 2026 Mathematics Senior Project. All Rights Reserved.
    </footer>

    <!-- Cursor Glow Follower -->
    <div id="cursorGlow" style="
        position: fixed;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.35) 0%, rgba(99, 102, 241, 0.15) 50%, transparent 70%);
        pointer-events: none;
        z-index: 1;
        transform: translate(-50%, -50%);
        transition: opacity 0.3s ease;
        opacity: 0;
    "></div>

    <!-- Particle Trail Canvas -->
    <canvas id="particleCanvas" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    "></canvas>

    <script>
        (function () {
            // === CURSOR GLOW ===
            const glow = document.getElementById('cursorGlow');
            let glowX = 0, glowY = 0;
            let targetX = 0, targetY = 0;

            document.addEventListener('mousemove', (e) => {
                targetX = e.clientX;
                targetY = e.clientY;
                glow.style.opacity = '1';
            });

            document.addEventListener('mouseleave', () => {
                glow.style.opacity = '0';
            });

            function animateGlow() {
                glowX += (targetX - glowX) * 0.08;
                glowY += (targetY - glowY) * 0.08;
                glow.style.left = glowX + 'px';
                glow.style.top = glowY + 'px';
                requestAnimationFrame(animateGlow);
            }
            animateGlow();

            // === PARTICLE TRAIL ===
            const canvas = document.getElementById('particleCanvas');
            const ctx = canvas.getContext('2d');
            let particles = [];
            let mouseX = 0, mouseY = 0;
            let lastSpawn = 0;

            function resizeCanvas() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;

                const now = Date.now();
                if (now - lastSpawn > 40) {
                    lastSpawn = now;
                    for (let i = 0; i < 2; i++) {
                        particles.push({
                            x: mouseX,
                            y: mouseY,
                            vx: (Math.random() - 0.5) * 2,
                            vy: (Math.random() - 0.5) * 2 - 0.5,
                            life: 1,
                            decay: 0.015 + Math.random() * 0.01,
                            size: 2 + Math.random() * 3,
                            hue: 220 + Math.random() * 30
                        });
                    }
                }
            });

            function animateParticles() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                for (let i = particles.length - 1; i >= 0; i--) {
                    const p = particles[i];
                    p.x += p.vx;
                    p.y += p.vy;
                    p.vy -= 0.02;
                    p.life -= p.decay;

                    if (p.life <= 0) {
                        particles.splice(i, 1);
                        continue;
                    }

                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.size * p.life, 0, Math.PI * 2);
                    ctx.fillStyle = `hsla(${p.hue}, 80%, 60%, ${p.life * 0.5})`;
                    ctx.fill();
                }

                if (particles.length > 150) {
                    particles = particles.slice(-150);
                }

                requestAnimationFrame(animateParticles);
            }
            animateParticles();
        })();
    </script>

</body>

</html>
