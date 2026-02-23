import sys
import json
import numpy as np
from scipy.optimize import linprog

# print("=== Berge equilibrium ===")

def phi1(x, y, A, B):
    # สมการ 21
    t1 = np.linalg.norm(x + A.dot(y))**2
    t2 = np.linalg.norm(x.dot(B) + y)**2
    return 0.25 * (t1 + t2)

def phi2(x, y, p, q, A, B):
    # สมการ 22
    t1 = np.linalg.norm(x - A.dot(y))**2
    t2 = np.linalg.norm(x.dot(B) - y)**2
    return 0.25 * (t1 + t2) + p + q

def calc_F(x, y, p, q, A, B):
    # สมการ 20
    return phi1(x, y, A, B) - phi2(x, y, p, q, A, B)

def calc_F_linear(x, y, p, q, A, B):
    # สมการ 14: F = x^T (A+B) y - p - q
    return x.dot(A + B).dot(y) - p - q

def get_gamma_bounds(A, B):
    # คำนวณขอบเขต Gamma ตามเงื่อนไขข้อ 1 (หน้า 8)
    m, n = A.shape
    vals = []
    for i in range(m):
        for j in range(n):
            x = np.zeros(m); x[i] = 1
            y = np.zeros(n); y[j] = 1
            p = np.max(A.dot(y))
            q = np.max(B.T.dot(x))
            val = phi2(x, y, p, q, A, B)
            vals.append(val)
    return min(vals), max(vals)

# --- 3. Algorithm 1: Local Search ---
def algorithm_1_local_search(A, B, y_start, max_iter=100, tol=1e-8):
    m, n = A.shape
    y_s = y_start.copy()
    q_s = np.max(B.dot(y_s))
    
    # กำหนดค่าเริ่มต้น
    x_next = np.zeros(m)
    p_next = 0
    y_next = y_s
    q_next = q_s
    F_val = 0
    
    for s in range(max_iter):
        # Step 1: Solve for x, p
        # Obj coeff for x: (A+B)y_s
        vec_obj_x = -1 * (A + B).dot(y_s)
        c1 = np.concatenate((vec_obj_x, [1]))
        
        # Constraint: x^T A - p <= 0
        # A_ub = [A.T, -1]
        A_ub1 = np.hstack((A.T, -np.ones((n, 1))))
        b_ub1 = np.zeros(n)
        
        # Equality: sum(x) = 1
        A_eq1 = np.hstack((np.ones((1, m)), np.zeros((1, 1))))
        b_eq1 = [1]
        bounds1 = [(0, None)]*m + [(None, None)]
        
        res1 = linprog(c1, A_ub=A_ub1, b_ub=b_ub1, A_eq=A_eq1, b_eq=b_eq1, bounds=bounds1, method='highs')
        if not res1.success: break
        x_next = res1.x[:m]
        p_next = res1.x[m]

        # Step 2: Solve for y, q
        # Obj coeff for y: x_next(A+B)
        vec_obj_y = -1 * x_next.dot(A + B)
        c2 = np.concatenate((vec_obj_y, [1]))
        
        # Constraint: B y - q <= 0
        # A_ub = [B, -1]
        A_ub2 = np.hstack((B, -np.ones((m, 1))))
        b_ub2 = np.zeros(m)
        
        # Equality: sum(y) = 1
        A_eq2 = np.hstack((np.ones((1, n)), np.zeros((1, 1))))
        b_eq2 = [1]
        bounds2 = [(0, None)]*n + [(None, None)]
        
        res2 = linprog(c2, A_ub=A_ub2, b_ub=b_ub2, A_eq=A_eq2, b_eq=b_eq2, bounds=bounds2, method='highs')
        if not res2.success: break
        y_next = res2.x[:n]
        q_next = res2.x[n]

        # Step 3: Check convergence
        F_val = calc_F_linear(x_next, y_next, p_next, q_next, A, B)
        F_compare = calc_F_linear(x_next, y_s, p_next, q_s, A, B)
        
        if abs(F_val - F_compare) <= tol:
            break
            
        y_s = y_next
        q_s = q_next
            
    return x_next, y_next, p_next, q_next, F_val

# --- 4. Algorithm 2: Global Search ---
def algorithm_2_global_search(A, B):
    m, n = A.shape
    epsilon = 1e-6
    mu = 10
    nu = 0.05
    
    # สร้าง Directions (Canonical Basis)
    directions = []
    for i in range(m):
        for j in range(n):
            u = np.zeros(m); u[i] = 1
            v = np.zeros(n); v[j] = 1
            directions.append((u, v))
    N = len(directions)
    # print(f"Generated {N} directions.")
    
    y0 = np.ones(n) / n
    gamma_min, gamma_max = get_gamma_bounds(A, B)
    gamma = gamma_min
    delta_gamma = (gamma_max - gamma_min) / mu
    s = 0
    
    # print(f"Gamma Range: [{gamma_min:.2f}, {gamma_max:.2f}]")

    # Step 1: Initial Local Search
    xk, yk, pk, qk, zeta_k = algorithm_1_local_search(A, B, y0)
    # print(f" -> Initial Local Trap: F = {zeta_k:.6f}")

    while True:
        # Step 2: Global Optimality Check
        if zeta_k >= -epsilon:
            return xk, yk, pk, qk, zeta_k
        
        if s >= N: s = 0 
        
        # Step 3: Construct Point
        u_s, v_s = directions[s]
        target = gamma + zeta_k
        val_phi1 = phi1(u_s, v_s, A, B)
        
        if val_phi1 < 1e-9 or target < 0:
            s += 1
            # Logic for loop control (Step 8 & 10 equivalent)
            if s >= N:
                if gamma < gamma_max: 
                    gamma += delta_gamma; 
                    s = 0; 
                    # print(f" [Expand] Gamma -> {gamma:.2f}")
                else: 
                    return xk, yk, pk, qk, zeta_k
            continue

        t = np.sqrt(target / val_phi1)
        u_bar = t * u_s
        v_bar = t * v_s
        p_bar = np.max(A.dot(v_bar))
        q_bar = np.max(B.T.dot(u_bar))
            
        # Step 4: Filtering
        if phi2(u_bar, v_bar, p_bar, q_bar, A, B) > gamma + (nu * gamma):
            s += 1
            if s >= N:
                if gamma < gamma_max: 
                    gamma += delta_gamma; 
                    s = 0; 
                    # print(f" [Expand] Gamma -> {gamma:.2f}")
                else: 
                    return xk, yk, pk, qk, zeta_k
            continue
            
        # Step 5: Local Search from Jump Point
        x_hat, y_hat, p_hat, q_hat, F_hat = algorithm_1_local_search(A, B, v_bar)
        
        # Step 6: Check Global Optimality
        if F_hat >= -epsilon:
            # print(f">>> STOP (Step 6): Found Global Solution! F = {F_hat:.6f}")
            return x_hat, y_hat, p_hat, q_hat, F_hat
        
        # Step 9: Improvement
        if F_hat > zeta_k + epsilon:
            # print(f" **IMPROVEMENT {zeta_k:.6f} -> {F_hat:.6f}")
            xk, yk, pk, qk = x_hat, y_hat, p_hat, q_hat
            zeta_k = F_hat
            s = 0
            gamma = gamma_min
            continue
            
        # Step 7, 8, 10: No Improvement
        s += 1
        if s >= N:
            if gamma < gamma_max:
                gamma += delta_gamma;
                s = 0
                # print(f" [Expand] Gamma -> {gamma:.2f}")
            # else:
                # print(f">>> STOP (Step 10): Exhausted. Best F = {zeta_k:.6f}")
    return xk, yk, pk, qk, zeta_k

# ส่วนรับข้อมูลจาก PHP และประมวลผล
def parse_matrix_string(data_str):
    # แปลงข้อความจากการ Copy Excel ให้เป็น Numpy Array
    rows = []
    lines = data_str.strip().split('\n')
    for line in lines:
        # รองรับทั้ง Tab และ Space
        parts = line.strip().split('\t') 
        if len(parts) == 1: 
             parts = line.strip().split()
             
        row = [float(x) for x in parts if x.strip() != '']
        if row:
            rows.append(row)
    return np.array(rows)

if __name__ == "__main__":
    try:
        # อ่านไฟล์ input จาก Argument ที่ PHP ส่งมา
        if len(sys.argv) < 2:
            raise ValueError("No input file provided")
        input_file = sys.argv[1]
        
        with open(input_file, 'r') as f:
            data = json.load(f)

        # แปลงข้อมูลเป็น Matrix
        A = parse_matrix_string(data['matrix_a'])
        B = parse_matrix_string(data['matrix_b'])

        # เรียกใช้อัลกอริทึม
        x, y, p, q, F = algorithm_2_global_search(A, B)

        # เตรียมผลลัพธ์เป็น Dictionary
        result = {
            "status": "success",
            "F": F,
            "p": p,
            "q": q,
            "x": x.tolist(),
            "y": y.tolist()
        }

    except Exception as e:
        result = {
            "status": "error",
            "message": str(e)
        }

    # พิมพ์ผลลัพธ์เป็น JSON บรรทัดเดียว เพื่อให้ PHP รับไปใช้งานได้
    print(json.dumps(result))