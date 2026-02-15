<!DOCTYPE html>
<html lang="th">
<head>
    <title>Game Theory Solver - Senior Project</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="dec.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="main-container d-flex align-items-center justify-content-center">
        <div class="card-custom text-center w-100" style="max-width: 800px; padding: 4rem 2rem;">
            
            <div class="mb-5">
                <span class="badge bg-light text-dark border mb-3">Senior Project 2026</span>
                <h1 class="project-title">Game Theory Equilibrium Solver</h1>
                <h2 class="h4 text-muted mb-4">Nash and Berge Equilibrium<br>Using Nonlinear Programming Method</h2>
                <div style="width: 60px; height: 4px; background: #2563eb; margin: 0 auto;"></div>
            </div>

            <p class="mb-5 text-secondary" style="font-size: 1.1rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                โปรแกรมสนับสนุนการตัดสินใจทางคณิตศาสตร์สำหรับทฤษฎีเกม<br>
                ด้วยวิธีการแก้ปัญหากำหนดการไม่เชิงเส้น (D.C. Optimization)<br>
                <small class="d-block mt-3 text-muted">สาขาวิชาคณิตศาสตร์และวิทยาการคอมพิวเตอร์<br>
                คณะวิทยาสาสตร์และเทคโนโลยี<br>มหาวิทยาลัยสงขลานครินทร์ วิทยาเขตปัตตานี</small>
            </p>

            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="cal_nash.php" class="btn btn-academic btn-lg px-4 gap-3">
                    Nash Equilibrium
                    <br><small style="font-size: 0.7em; opacity: 0.8; font-weight: 300;">จุดสมดุลแนช</small>
                </a>
                <a href="cal_berge.php" class="btn btn-academic btn-lg px-4">
                    Berge Equilibrium
                    <br><small style="font-size: 0.7em; opacity: 0.8; font-weight: 300;">จุดสมดุลเบิร์จ</small>
                </a>
            </div>

        </div>
    </div>

    <footer class="academic-footer">
        &copy; 2026 Mathematics Senior Project. All Rights Reserved.
    </footer>

</body>
</html>