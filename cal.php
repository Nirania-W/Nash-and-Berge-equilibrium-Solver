<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรแกรมหาจุดสมดุล</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        textarea {
            font-family: monospace;
            white-space: pre;
            overflow-x: scroll;
            height: 200px;
        }
        .matrix-input-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="text-center mb-4">🧮 โปรแกรมหาจุดสมดุล Berge</h2>

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
        $scriptPath = __DIR__ . DIRECTORY_SEPARATOR . 'solver.py';

        // 
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

    <form method="post" action="">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="matrix-input-label">Matrix A (Payoff Player 1)</label>
                <div class="alert alert-info py-1 small">
                    <i class="bi bi-info-circle"></i> วิธีใช้: Copy ข้อมูลตัวเลขจาก Excel (ลากคลุมแล้ว Ctrl+C) มา Paste ในช่องนี้ได้เลย
                </div>
                <textarea name="matrixA" class="form-control" placeholder="วางข้อมูล Matrix A ที่นี่..." required><?php echo isset($_POST['matrixA']) ? htmlspecialchars($_POST['matrixA']) : ''; ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="matrix-input-label">Matrix B (Payoff Player 2)</label>
                <div class="alert alert-info py-1 small">
                    <i class="bi bi-info-circle"></i> วิธีใช้: Copy ข้อมูลตัวเลขจาก Excel (ลากคลุมแล้ว Ctrl+C) มา Paste ในช่องนี้ได้เลย
                </div>
                <textarea name="matrixB" class="form-control" placeholder="วางข้อมูล Matrix B ที่นี่..." required><?php echo isset($_POST['matrixB']) ? htmlspecialchars($_POST['matrixB']) : ''; ?></textarea>
            </div>
        </div>
        
        <div class="d-grid gap-2 col-6 mx-auto mt-3">
            <button type="submit" class="btn btn-primary btn-lg">🚀 คำนวณหาคำตอบ</button>
        </div>
    </form>

    <hr class="my-5">

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($result && $result['status'] == 'success'): ?>
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">ผลลัพธ์การคำนวณ</h4>
            </div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-md-4">
                        <h5>ค่าฟังก์ชัน F*</h5>
                        <h2 class="text-primary"><?php echo number_format($result['F'], 6); ?></h2>
                    </div>
                    <div class="col-md-4">
                        <h5>ขอบเขต p*</h5>
                        <h3><?php echo number_format($result['p'], 4); ?></h3>
                    </div>
                    <div class="col-md-4">
                        <h5>ขอบเขต q*</h5>
                        <h3><?php echo number_format($result['q'], 4); ?></h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5>กลยุทธ์ผู้เล่นที่ 1 (x*)</h5>
                        <table class="table table-bordered table-striped">
                            <?php foreach ($result['x'] as $idx => $val): ?>
                            <tr>
                                <th>แถวที่ <?php echo $idx + 1; ?></th>
                                <td><?php echo number_format($val, 6); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>กลยุทธ์ผู้เล่นที่ 2 (y*)</h5>
                        <table class="table table-bordered table-striped">
                            <?php foreach ($result['y'] as $idx => $val): ?>
                            <tr>
                                <th>หลักที่ <?php echo $idx + 1; ?></th>
                                <td><?php echo number_format($val, 6); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

</body>
</html>