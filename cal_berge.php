<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berge Equilibrium — Game Theory Solver</title>
    <meta name="description" content="คำนวณจุดสมดุลเบิร์จ (Berge Equilibrium) ด้วยวิธีกำหนดการไม่เชิงเส้น">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dec.css">
</head>

<body>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <canvas id="mathMatrixCanvas"></canvas>
        <div class="calculation-core">
            <div class="hud-outer-ring"></div>
        </div>
        <div class="loading-text" id="glitchText" data-text="กำลังน่ารัก... เอ้ย กำลังคำนวณ">กำลังน่ารัก... เอ้ย กำลังคำนวณ</div>
    </div>

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

    <!-- Formal Navbar -->
    <nav class="navbar navbar-formal">
        <div class="container-fluid" style="max-width: 1000px; margin: 0 auto;">
            <a class="navbar-brand" href="index.php">
                Game Theory Solver
            </a>
            <div class="d-flex">
                <a href="index.php" class="btn-back">หน้าหลัก</a>
            </div>
        </div>
    </nav>

    <div class="main-container">

        <!-- Page Header -->
        <div class="page-header">
            <h2>Berge Equilibrium</h2>
            <p>การคำนวณจุดสมดุลเบิร์จ</p>
        </div>

        <?php
        $result = null;
        $error = null;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $matrixA_str = $_POST['matrixA'];
            $matrixB_str = $_POST['matrixB'];

            $inputData = json_encode([
                "matrix_a" => $matrixA_str,
                "matrix_b" => $matrixB_str
            ]);

            $tempFile = tempnam(sys_get_temp_dir(), 'math_input');
            file_put_contents($tempFile, $inputData);

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $pythonExec = "python";
            } else {
                $pythonExec = "python3";
            }

            $scriptPath = __DIR__ . DIRECTORY_SEPARATOR . 'solver_berge.py';
            $command = $pythonExec . " " . escapeshellarg($scriptPath) . " " . escapeshellarg($tempFile) . " 2>&1";
            $output = shell_exec($command);

            unlink($tempFile);

            if ($output) {
                $result = json_decode($output, true);
                if (json_last_error() !== JSON_ERROR_NONE || (isset($result['status']) && $result['status'] == 'error')) {
                    $error = "เกิดข้อผิดพลาดจาก Python: " . ($result['message'] ?? 'Unknown error') . "<br>Raw Output: " . htmlspecialchars($output);
                    $result = null;
                }
            } else {
                $error = "ไม่สามารถรัน Python Script ได้";
            }
        }
        ?>

        <!-- Input Form Card -->
        <div class="card-custom reveal">
            <div class="info-box mb-4">
                <strong>คำแนะนำ:</strong> คัดลอกข้อมูลตัวเลขจาก Excel (เฉพาะตัวเลข) มาวางได้ทันที
                ข้อมูลจะจัดรูปแบบอัตโนมัติ<br>
                <span class="text-warning-note">⚠ หมายเหตุ: โปรแกรมรองรับจำนวนเต็ม ทศนิยม เศษส่วน (เช่น 1/2, -3/4)
                    และค่าติดรากที่สอง (เช่น sqrt(2), √3, 2sqrt(5))</span>
            </div>

            <form method="post" action="">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="matrixA" class="label-header">Matrix A (ผู้เล่นที่ 1)</label>
                        <div class="matrix-size-badge" id="sizeA">📐 —</div>
                        <textarea name="matrixA" id="matrixA" class="form-control matrix-input-area" rows="8"
                            placeholder="1  2  3&#10;4  5  6"
                            required><?php echo isset($_POST['matrixA']) ? htmlspecialchars($_POST['matrixA']) : ''; ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="matrixB" class="label-header">Matrix B (ผู้เล่นที่ 2)</label>
                        <div class="matrix-size-badge" id="sizeB">📐 —</div>
                        <textarea name="matrixB" id="matrixB" class="form-control matrix-input-area" rows="8"
                            placeholder="7  8  9&#10;1  2  3"
                            required><?php echo isset($_POST['matrixB']) ? htmlspecialchars($_POST['matrixB']) : ''; ?></textarea>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <button type="submit" name="calculate" id="btnCalculate" class="btn btn-academic btn-lg" style="min-width: 280px;">
                        คำนวณ (Calculate)
                    </button>
                </div>
            </form>
        </div>

        <!-- Error Alert -->
        <?php if ($error): ?>
            <div class="error-alert">
                <h5>!! พบข้อผิดพลาด</h5>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Results Section -->
        <?php if ($result && $result['status'] == 'success'): ?>
            <div class="card-custom results-card reveal success-pulse">
                <div class="results-actions">
                    <button class="btn-copy" id="btnCopyResults" onclick="copyResults()">
                        คัดลอกผลลัพธ์
                    </button>
                </div>
                <div class="section-title">
                    ผลลัพธ์การคำนวณ (Calculation Results)
                </div>

                <div class="row mb-5 g-3">
                    <div class="col-md-4">
                        <div class="result-box reveal reveal-delay-1 tooltip-custom" data-tooltip="ค่า F* ยิ่งใกล้ 0 ยิ่งดี = จุดสมดุลที่แท้จริง">
                            <div class="result-label">Objective Function (F*)</div>
                            <div class="result-value">
                                <?php
                                $parts = explode('e', sprintf("%.3e", $result['F']));
                                $exp = (int) $parts[1];

                                if ($exp == 0) {
                                    echo $parts[0];
                                } else {
                                    echo $parts[0] . ' &times; 10<sup>' . $exp . '</sup>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="result-box reveal reveal-delay-2 tooltip-custom" data-tooltip="ค่าผลตอบแทนสูงสุดของผู้เล่นที่ 1">
                            <div class="result-label">Payoff Bound (p*)</div>
                            <div class="result-value" data-target="<?php echo number_format($result['p'], 3); ?>"><?php echo number_format($result['p'], 3); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="result-box reveal reveal-delay-3 tooltip-custom" data-tooltip="ค่าผลตอบแทนสูงสุดของผู้เล่นที่ 2">
                            <div class="result-label">Payoff Bound (q*)</div>
                            <div class="result-value" data-target="<?php echo number_format($result['q'], 3); ?>"><?php echo number_format($result['q'], 3); ?></div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 reveal reveal-delay-2">
                    <div class="col-md-6">
                        <h5 class="strategy-title">กลยุทธ์ผู้เล่นที่ 1 (Row Player)</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-academic mb-0">
                                <thead>
                                    <tr>
                                        <th class="w-25 text-center">กลยุทธ์ที่</th>
                                        <th class="text-center">ความน่าจะเป็น (Probability)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result['x'] as $idx => $val): ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?php echo $idx + 1; ?></td>
                                            <td class="text-end pe-4 <?php echo ($val > 0.0001) ? 'value-highlight' : ''; ?>">
                                                <?php echo number_format($val, 4); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="strategy-title">กลยุทธ์ผู้เล่นที่ 2 (Column Player)</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-academic mb-0">
                                <thead>
                                    <tr>
                                        <th class="w-25 text-center">กลยุทธ์ที่</th>
                                        <th class="text-center">ความน่าจะเป็น (Probability)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result['y'] as $idx => $val): ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?php echo $idx + 1; ?></td>
                                            <td class="text-end pe-4 <?php echo ($val > 0.0001) ? 'value-highlight' : ''; ?>">
                                                <?php echo number_format($val, 4); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== สรุปคำตอบ (Summary) ==================== -->
                <div class="summary-section reveal reveal-delay-3">
                    <div class="summary-section-title">สรุปความน่าจะเป็นของกลยุทธ์ที่ควรเลือกใช้ (Strategy Summary)</div>

                    <div class="row g-4">
                        <!-- Player 1 Summary -->
                        <div class="col-md-6">
                            <div class="summary-player-card">
                                <div class="summary-player-label">ผู้เล่นที่ 1 (Row Player)</div>
                                <?php
                                $sumX = array_sum($result['x']);
                                $activeX = [];
                                foreach ($result['x'] as $idx => $val):
                                    $pct = ($sumX > 0) ? ($val / $sumX) * 100 : 0;
                                    $isActive = $pct > 0.01;
                                    if ($isActive) {
                                        $activeX[] = 'กลยุทธ์ที่ ' . ($idx + 1);
                                    } else {
                                        continue;
                                    }
                                    ?>
                                    <div class="summary-bar-row">
                                        <span class="summary-bar-label">กลยุทธ์ที่ <?php echo $idx + 1; ?></span>
                                        <div class="summary-bar-track">
                                            <div class="summary-bar-fill active" style="width: <?php echo max($pct, 0.5); ?>%;">
                                                <?php if ($pct >= 8): ?>
                                                    <span class="summary-bar-percent"><?php echo number_format($pct, 2); ?>%</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <span class="summary-bar-percent-outside"><?php echo number_format($pct, 2); ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                                <div class="summary-total-badge">
                                    รวมเป็น
                                    <?php echo number_format(array_sum(array_map(function ($v) use ($sumX) {
                                        return ($sumX > 0) ? ($v / $sumX) * 100 : 0; }, $result['x'])), 1); ?>%
                                </div>
                            </div>
                        </div>

                        <!-- Player 2 Summary -->
                        <div class="col-md-6">
                            <div class="summary-player-card">
                                <div class="summary-player-label">ผู้เล่นที่ 2 (Column Player)</div>
                                <?php
                                $sumY = array_sum($result['y']);
                                $activeY = [];
                                foreach ($result['y'] as $idx => $val):
                                    $pct = ($sumY > 0) ? ($val / $sumY) * 100 : 0;
                                    $isActive = $pct > 0.01;
                                    if ($isActive) {
                                        $activeY[] = 'กลยุทธ์ที่ ' . ($idx + 1);
                                    } else {
                                        continue;
                                    }
                                    ?>
                                    <div class="summary-bar-row">
                                        <span class="summary-bar-label">กลยุทธ์ที่ <?php echo $idx + 1; ?></span>
                                        <div class="summary-bar-track">
                                            <div class="summary-bar-fill active" style="width: <?php echo max($pct, 0.5); ?>%;">
                                                <?php if ($pct >= 8): ?>
                                                    <span class="summary-bar-percent"><?php echo number_format($pct, 2); ?>%</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <span class="summary-bar-percent-outside"><?php echo number_format($pct, 2); ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                                <div class="summary-total-badge">
                                    รวมเป็น
                                    <?php echo number_format(array_sum(array_map(function ($v) use ($sumY) {
                                        return ($sumY > 0) ? ($v / $sumY) * 100 : 0; }, $result['y'])), 1); ?>%
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Insight Card -->
                    <div class="summary-insight">
                        <div class="summary-insight-title">สรุปกลยุทธ์ที่ใช้จริง (Active Strategies)</div>
                        <p class="summary-insight-text">
                            <strong>ผู้เล่นที่ 1</strong> ใช้กลยุทธ์:
                            <?php foreach ($activeX as $s): ?>
                                <span class="summary-strategy-chip"><?php echo $s; ?></span>
                            <?php endforeach; ?>
                            <br>
                            <strong>ผู้เล่นที่ 2</strong> ใช้กลยุทธ์:
                            <?php foreach ($activeY as $s): ?>
                                <span class="summary-strategy-chip"><?php echo $s; ?></span>
                            <?php endforeach; ?>
                        </p>
                    </div>
                </div>

            </div>

            <!-- ==================== Local Solutions ==================== -->
            <?php if (!empty($result['local_solutions'])): ?>
            <div class="card-custom reveal" style="margin-top: 2rem;">
                <div class="section-title">
                    Local Solutions ที่ค้นพบระหว่างการค้นหา
                </div>

                <div class="info-box mb-4">
                    <strong>📊 สรุป:</strong>
                    พบ <strong><?php echo count($result['local_solutions']); ?></strong> local solution(s) ระหว่างการค้นหา
                    &nbsp;|&nbsp;
                    Global Solution ตรงกับ <strong>Local Solution #<?php echo $result['global_solution_index']; ?></strong>
                </div>

                <div class="local-solutions-list">
                <?php foreach ($result['local_solutions'] as $sol): ?>
                    <div class="local-solution-card <?php echo $sol['is_global'] ? 'is-global-solution' : ''; ?>">
                        <div class="local-solution-header">
                            <span class="local-solution-number">#<?php echo $sol['index']; ?></span>
                            <span class="local-solution-source"><?php echo htmlspecialchars($sol['source']); ?></span>
                            <span class="local-solution-gamma">γ = <?php echo number_format($sol['gamma'], 4); ?></span>
                            <?php if ($sol['is_global']): ?>
                                <span class="global-solution-badge">★ Global Solution</span>
                            <?php endif; ?>
                        </div>
                        <div class="local-solution-body">
                            <div class="local-solution-metrics">
                                <div class="local-metric">
                                    <span class="local-metric-label">F*</span>
                                    <span class="local-metric-value"><?php
                                        $parts = explode('e', sprintf("%.3e", $sol['F']));
                                        $exp = (int) $parts[1];
                                        echo ($exp == 0) ? $parts[0] : $parts[0] . ' &times; 10<sup>' . $exp . '</sup>';
                                    ?></span>
                                </div>
                                <div class="local-metric">
                                    <span class="local-metric-label">p*</span>
                                    <span class="local-metric-value"><?php echo number_format($sol['p'], 4); ?></span>
                                </div>
                                <div class="local-metric">
                                    <span class="local-metric-label">q*</span>
                                    <span class="local-metric-value"><?php echo number_format($sol['q'], 4); ?></span>
                                </div>
                            </div>
                            <div class="local-solution-strategies">
                                <div class="local-strategy">
                                    <span class="local-strategy-label">x (Player 1):</span>
                                    <span class="local-strategy-values">[<?php echo implode(', ', array_map(fn($v) => number_format($v, 4), $sol['x'])); ?>]</span>
                                </div>
                                <div class="local-strategy">
                                    <span class="local-strategy-label">y (Player 2):</span>
                                    <span class="local-strategy-values">[<?php echo implode(', ', array_map(fn($v) => number_format($v, 4), $sol['y'])); ?>]</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <footer class="academic-footer">
        &copy; <?php echo date("Y"); ?> Mathematics Senior Project. All rights reserved.
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

    <!-- Toast Notification Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
        (function () {
            // === Cursor Glow ===
            const glow = document.getElementById('cursorGlow');
            let glowX = 0, glowY = 0, targetX = 0, targetY = 0;
            document.addEventListener('mousemove', (e) => { targetX = e.clientX; targetY = e.clientY; glow.style.opacity = '1'; });
            document.addEventListener('mouseleave', () => { glow.style.opacity = '0'; });
            function animateGlow() { glowX += (targetX - glowX) * 0.08; glowY += (targetY - glowY) * 0.08; glow.style.left = glowX + 'px'; glow.style.top = glowY + 'px'; requestAnimationFrame(animateGlow); }
            animateGlow();

            // === Particle Trail ===
            const canvas = document.getElementById('particleCanvas');
            const ctx = canvas.getContext('2d');
            let particles = [], mouseX = 0, mouseY = 0, lastSpawn = 0;
            function resizeCanvas() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
            resizeCanvas(); window.addEventListener('resize', resizeCanvas);
            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX; mouseY = e.clientY;
                const now = Date.now();
                if (now - lastSpawn > 40) { lastSpawn = now; for (let i = 0; i < 2; i++) { particles.push({ x: mouseX, y: mouseY, vx: (Math.random()-0.5)*2, vy: (Math.random()-0.5)*2-0.5, life: 1, decay: 0.015+Math.random()*0.01, size: 2+Math.random()*3, hue: 220+Math.random()*30 }); } }
            });
            function animateParticles() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                for (let i = particles.length - 1; i >= 0; i--) { const p = particles[i]; p.x += p.vx; p.y += p.vy; p.vy -= 0.02; p.life -= p.decay; if (p.life <= 0) { particles.splice(i, 1); continue; } ctx.beginPath(); ctx.arc(p.x, p.y, p.size * p.life, 0, Math.PI * 2); ctx.fillStyle = `hsla(${p.hue}, 80%, 60%, ${p.life * 0.5})`; ctx.fill(); }
                if (particles.length > 150) particles = particles.slice(-150);
                requestAnimationFrame(animateParticles);
            }
            animateParticles();

            // === Scroll Reveal (IntersectionObserver) ===
            const revealEls = document.querySelectorAll('.reveal');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('revealed'); observer.unobserve(entry.target); } });
            }, { threshold: 0.15, rootMargin: '0px 0px -30px 0px' });
            revealEls.forEach(el => observer.observe(el));

            // === Button Ripple Effect ===
            document.querySelectorAll('.btn-academic').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
                    ripple.style.top = (e.clientY - rect.top - size/2) + 'px';
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // === Loading Overlay on Submit ===
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', () => {
                    document.getElementById('loadingOverlay').classList.add('active');
                });
            }

            // === Matrix Size Detector ===
            function detectMatrixSize(textarea, badge) {
                const val = textarea.value.trim();
                if (!val) { badge.classList.remove('visible'); return; }
                const lines = val.split('\n').filter(l => l.trim());
                const rows = lines.length;
                const cols = lines[0] ? lines[0].trim().split(/[\t\s]+/).filter(x => x).length : 0;
                badge.textContent = `📐 ${rows} × ${cols}`;
                badge.classList.add('visible');
            }
            const matA = document.getElementById('matrixA');
            const matB = document.getElementById('matrixB');
            const sizeA = document.getElementById('sizeA');
            const sizeB = document.getElementById('sizeB');
            if (matA && sizeA) { matA.addEventListener('input', () => detectMatrixSize(matA, sizeA)); detectMatrixSize(matA, sizeA); }
            if (matB && sizeB) { matB.addEventListener('input', () => detectMatrixSize(matB, sizeB)); detectMatrixSize(matB, sizeB); }

            // === Number Counter Animation ===
            document.querySelectorAll('.result-value[data-target]').forEach(el => {
                const target = parseFloat(el.getAttribute('data-target'));
                if (isNaN(target)) return;
                let current = 0;
                const duration = 800;
                const start = performance.now();
                function animate(now) {
                    const progress = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    current = target * eased;
                    el.textContent = current.toFixed(3);
                    if (progress < 1) requestAnimationFrame(animate);
                    else { el.textContent = target.toFixed(3); el.setAttribute('data-counted', ''); }
                }
                requestAnimationFrame(animate);
            });

            // === Confetti Celebration ===
            const resultsCard = document.querySelector('.results-card');
            if (resultsCard) {
                setTimeout(() => {
                    const rect = resultsCard.getBoundingClientRect();
                    const colors = ['#1e40af', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#059669', '#fbbf24'];
                    for (let i = 0; i < 30; i++) {
                        const particle = document.createElement('div');
                        particle.className = 'confetti-particle';
                        particle.style.left = (rect.left + Math.random() * rect.width) + 'px';
                        particle.style.top = (rect.top + 20) + 'px';
                        particle.style.background = colors[Math.floor(Math.random() * colors.length)];
                        particle.style.animationDelay = (Math.random() * 0.5) + 's';
                        particle.style.animationDuration = (1 + Math.random()) + 's';
                        document.body.appendChild(particle);
                        setTimeout(() => particle.remove(), 2500);
                    }
                }, 300);
            }

            // === 3D Dynamic Content Tilt ===
            document.querySelectorAll('.card-custom').forEach(card => {
                card.addEventListener('mousemove', e => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    const rotateX = ((y - centerY) / centerY) * -3; // max tilt degrees
                    const rotateY = ((x - centerX) / centerX) * 3;
                    
                    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-2px)`;
                    card.style.transition = 'none';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0)`;
                    card.style.transition = 'transform 0.5s cubic-bezier(0.25, 1, 0.5, 1)';
                });
            });

            // === Repel Floating Symbols (if present) ===
            const symbols = document.querySelectorAll('.floating-symbols .symbol, .bg-decoration .shape');
            document.addEventListener('mousemove', e => {
                symbols.forEach(sym => {
                    const rect = sym.getBoundingClientRect();
                    const symX = rect.left + rect.width / 2;
                    const symY = rect.top + rect.height / 2;
                    const distX = e.clientX - symX;
                    const distY = e.clientY - symY;
                    const distance = Math.sqrt(distX*distX + distY*distY);
                    
                    if(distance < 150) {
                        const repelX = (distX / distance) * -20;
                        const repelY = (distY / distance) * -20;
                        sym.style.transform = `translate(${repelX}px, ${repelY}px) scale(1.1)`;
                    } else {
                        sym.style.transform = '';
                    }
                });
            });

        })();

        // === Custom Toast System ===
        function showToast(message, icon = '✅') {
            const container = document.getElementById('toastContainer');
            if(!container) return;
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = `<span class="toast-icon">${icon}</span><span class="toast-message">${message}</span>`;
            container.appendChild(toast);
            
            // Trigger animation
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    toast.classList.add('toast-show');
                });
            });
            
            setTimeout(() => {
                toast.classList.remove('toast-show');
                setTimeout(() => toast.remove(), 400); // Wait for CSS transition
            }, 3000);
        }

        // === Copy Results ===
        function copyResults() {
            const resultBoxes = document.querySelectorAll('.result-box');
            let text = '=== ผลลัพธ์การคำนวณ ===\n';
            resultBoxes.forEach(box => {
                const label = box.querySelector('.result-label');
                const value = box.querySelector('.result-value');
                if (label && value) text += label.textContent + ': ' + value.textContent.trim() + '\n';
            });
            const tables = document.querySelectorAll('.table-academic');
            tables.forEach((table, idx) => {
                text += '\n' + (idx === 0 ? '--- ผู้เล่นที่ 1 ---' : '--- ผู้เล่นที่ 2 ---') + '\n';
                table.querySelectorAll('tbody tr').forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 2) text += 'กลยุทธ์ที่ ' + cells[0].textContent.trim() + ': ' + cells[1].textContent.trim() + '\n';
                });
            });
            navigator.clipboard.writeText(text).then(() => {
                showToast('คัดลอกผลลัพธ์สำเร็จแล้ว!');
                const btn = document.getElementById('btnCopyResults');
                btn.classList.add('copied');
                btn.innerHTML = 'คัดลอกแล้ว!';
                setTimeout(() => { btn.classList.remove('copied'); btn.innerHTML = 'คัดลอกผลลัพธ์'; }, 2000);
            });
        }
    </script>

    <!-- Matrix Rain Canvas JavaScript -->
    <script>
        const canvas = document.getElementById('mathMatrixCanvas');
        const ctx = canvas.getContext('2d');
        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        const mathChars = 'NashBergeNash01∑∫π∞∇∆θλμρ'.split('');
        
        // 3D Space Tunnel / Radial Matrix
        const stars = Array(200).fill().map(() => {
            return {
                x: Math.random() * width - width / 2,
                y: Math.random() * height - height / 2,
                z: Math.random() * width,
                text: mathChars[Math.floor(Math.random() * mathChars.length)]
            };
        });

        function drawRadialMatrix() {
            ctx.fillStyle = 'rgba(2, 5, 10, 0.2)'; // Intense motion blur trail
            ctx.fillRect(0, 0, width, height);

            const centerX = width / 2;
            const centerY = height / 2;

            for (let i = 0; i < stars.length; i++) {
                const star = stars[i];
                star.z -= 15; // Speed of warp travel
                
                if (star.z <= 0) {
                    star.x = Math.random() * width - centerX;
                    star.y = Math.random() * height - centerY;
                    star.z = width;
                    star.text = mathChars[Math.floor(Math.random() * mathChars.length)];
                }

                // 3D projection mathematical mapping
                const sx = star.x / (star.z / width) + centerX;
                const sy = star.y / (star.z / width) + centerY;
                const scale = (1 - star.z / width);
                const size = scale * 30; // Closer = bigger font
                
                // Color fading based on depth
                const alpha = scale;
                
                // Only draw if traversing visible screen
                if (sx > 0 && sx < width && sy > 0 && sy < height) {
                    ctx.font = `bold ${size}px "JetBrains Mono", "Courier New", monospace`;
                    if (scale > 0.85) {
                        ctx.fillStyle = '#ffffff';
                        ctx.shadowBlur = 20;
                        ctx.shadowColor = '#818cf8';
                    } else {
                        ctx.fillStyle = `rgba(130, 180, 255, ${alpha})`;
                        ctx.shadowBlur = 0;
                    }
                    ctx.fillText(star.text, sx, sy);
                }
            }
            if (document.getElementById('loadingOverlay').classList.contains('active')) {
                requestAnimationFrame(drawRadialMatrix);
            }
        }
        
        // Text Decode Hacker Effect
        function runDecodeEffect() {
            const el = document.getElementById('glitchText');
            const finalTxt = el.getAttribute('data-text');
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!<>-_\\/[]{}—=+*^?#';
            let iteration = 0;
            let decodeInterval = setInterval(() => {
                el.innerText = finalTxt.split('').map((letter, index) => {
                    if (index < iteration) return finalTxt[index];
                    return chars[Math.floor(Math.random() * chars.length)];
                }).join('');
                if (iteration >= finalTxt.length) clearInterval(decodeInterval);
                iteration += 1/3;
            }, 30);
        }
        
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('loadingOverlay').classList.add('active');
            requestAnimationFrame(drawRadialMatrix);
            runDecodeEffect();
            
            // Cinematic artificial delay to show off the GOD-TIER Loading Screen
            setTimeout(() => {
                this.submit();
            }, 2800);
        });
        
        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });
    </script>
</body>

</html>