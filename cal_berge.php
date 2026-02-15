<!DOCTYPE html>
<html lang="th">
<header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คำนวณ Berge Equilibrium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dec.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid max-width-1000" style="max-width: 1000px; margin: 0 auto;">
            <a class="navbar-brand fw-bold text-primary" href="index.php">Game Theory Solver</a>
            <div class="d-flex">
                <a href="index.php" class="btn btn-outline-secondary btn-sm">กลับหน้าหลัก</a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        
        <div class="text-center mb-5 mt-4">
            <h2 class="display-6 fw-bold text-dark">Berge Equilibrium</h2>
            <p class="text-muted">การคำนวณจุดสมดุลเบิร์จ</p>
        </div>

        <?php
        $result = null;
        $error = null;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $matrixA_str = $_POST['matrixA'];
            $matrixB_str = $_POST['matrixB'];

            // เตรียมข้อมูลใส่ไฟล์ JSON (เพื่อความปลอดภัยเวลามีข้อมูลเยอะๆ)
            $inputData = json_encode([
                "matrix_a" => $matrixA_str,
                "matrix_b" => $matrixB_str
            ]);
            
            $tempFile = tempnam(sys_get_temp_dir(), 'math_input');
            file_put_contents($tempFile, $inputData);

            // === เรียก Python Script
            // ตรวจสอบ OS ว่าเป็น Windows หรือ Linux/Mac เพื่อเลือกคำสั่ง
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $pythonExec = "python"; // Windows
            } else {
                $pythonExec = "python3"; // Linux/Mac
            }

            // ระบุไฟล์ solver.py โดยอ้างอิงจากโฟลเดอร์ปัจจุบัน (__DIR__)
            $scriptPath = __DIR__ . DIRECTORY_SEPARATOR . 'solver_berge.py';
            $command = $pythonExec . " " . escapeshellarg($scriptPath) . " " . escapeshellarg($tempFile) . " 2>&1";
            $output = shell_exec($command);
            
            // ลบไฟล์ชั่วคราวทิ้ง
            unlink($tempFile);

            // 3. แปลงผลลัพธ์กลับมาใช้งาน
            if ($output) {
                $result = json_decode($output, true);
                if(json_last_error() !== JSON_ERROR_NONE || (isset($result['status']) && $result['status'] == 'error')) {
                $error = "เกิดข้อผิดพลาดจาก Python: " . ($result['message'] ?? 'Unknown error') . "<br>Raw Output: " . htmlspecialchars($output);
                $result = null;
                }
            } else {
                $error = "ไม่สามารถรัน Python Script ได้";
            }
        }
        ?>

        <div class="card-custom">
            <div class="alert alert-light border mb-4">
                <i class="bi bi-info-circle-fill text-primary"></i> 
                <strong>คำแนะนำ:</strong> คัดลอกข้อมูลตัวเลขจาก Excel (เฉพาะตัวเลข) มาวางได้ทันที ข้อมูลจะจัดรูปแบบอัตโนมัติ
            </div>

            <form method="post" action="">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="matrixA" class="label-header">Matrix A (ผู้เล่นที่ 1)</label>
                        <textarea name="matrixA" id="matrixA" class="form-control matrix-input-area" rows="8" placeholder="1  2  3&#10;4  5  6" required><?php echo isset($_POST['matrixA']) ? htmlspecialchars($_POST['matrixA']) : ''; ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="matrixB" class="label-header">Matrix B (ผู้เล่นที่ 2)</label>
                        <textarea name="matrixB" id="matrixB" class="form-control matrix-input-area" rows="8" placeholder="7  8  9&#10;1  2  3" required><?php echo isset($_POST['matrixB']) ? htmlspecialchars($_POST['matrixB']) : ''; ?></textarea>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <button type="submit" name="calculate" class="btn btn-academic btn-lg w-50">
                        คำนวณผลลัพธ์ (Calculate)
                    </button>
                </div>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger shadow-sm border-0 border-start border-danger border-4">
                <h5 class="alert-heading">พบข้อผิดพลาด</h5>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($result && $result['status'] == 'success'): ?>
            <div class="card-custom border-top border-success border-4" style="border-top-width: 4px !important;">
                <h3 class="mb-4 pb-2 border-bottom">ผลลัพธ์การคำนวณ (Calculation Results)</h3>
                
                <div class="row mb-5 g-3">
                    <div class="col-md-4">
                        <div class="result-box">
                            <div class="text-muted small text-uppercase mb-2">Objective Function (F*)</div>
                            <div class="result-value text-success"><?php echo number_format($result['F'], 6); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="result-box">
                            <div class="text-muted small text-uppercase mb-2">Payoff Bound (p*)</div>
                            <div class="result-value text-dark"><?php echo number_format($result['p'], 4); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="result-box">
                            <div class="text-muted small text-uppercase mb-2">Payoff Bound (q*)</div>
                            <div class="result-value text-dark"><?php echo number_format($result['q'], 4); ?></div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <h5 class="mb-3 text-primary">กลยุทธ์ผู้เล่นที่ 1 (Row Player)</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-academic mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="w-25 text-center">แถวที่</th>
                                        <th class="text-center">ความน่าจะเป็น (Probability)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result['x'] as $idx => $val): ?>
                                    <tr>
                                        <td class="text-center fw-bold bg-light"><?php echo $idx + 1; ?></td>
                                        <td class="text-end pe-4 <?php echo ($val > 0.0001) ? 'fw-bold text-primary' : 'text-muted'; ?>">
                                            <?php echo number_format($val, 6); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3 text-primary">กลยุทธ์ผู้เล่นที่ 2 (Column Player)</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-academic mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="w-25 text-center">หลักที่</th>
                                        <th class="text-center">ความน่าจะเป็น (Probability)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result['y'] as $idx => $val): ?>
                                    <tr>
                                        <td class="text-center fw-bold bg-light"><?php echo $idx + 1; ?></td>
                                        <td class="text-end pe-4 <?php echo ($val > 0.0001) ? 'fw-bold text-primary' : 'text-muted'; ?>">
                                            <?php echo number_format($val, 6); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="academic-footer">
        &copy; <?php echo date("Y"); ?> Mathematics Senior Project. All rights reserved.
    </footer>
</body>
</html>