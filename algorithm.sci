function [x_opt, y_opt, p_opt, q_opt, F_val] = berge_local_search(A, B, y0, max_iter, tol)
    
    [m, n] = size(A);
    
    // Set s = 0, y^s = y0
    s = 0;
    y_curr = y0;
    
    p_curr = 0; 
    q_curr = 0;
    x_curr = zeros(m, 1);
    
    printf("Local Search (Y-Procedure)...\n");
    
    for s = 1:max_iter
        c1 = [-((A + B) * y_curr); 1];
        
        num_vars1 = m + 1; // x (size m) + q (size 1)
        Q1 = zeros(num_vars1, num_vars1);

        C1_ineq = [B', -ones(n, 1)];
        b1_ineq = zeros(n, 1);
        
        // Equality Matrix (C_eq * z = b_eq)
        C1_eq = [ones(1, m), 0];
        b1_eq = 1;
        
        // Combine constraints for qpsolve (Equality constraints come first)
        C1 = [C1_eq; C1_ineq];
        b1 = [b1_eq; b1_ineq];
        me1 = 1; // Number of equality constraints
        
        // Bounds (x >= 0, q is free but we set large bounds)
        ci1 = [zeros(m, 1); -10000]; // lower bound
        cs1 = [ones(m, 1) * %inf; 10000]; // upper bound
        
        // Solve LP Step 1
        [z1_opt, lagr1, f1_opt] = qpsolve(Q1, c1, C1, b1, ci1, cs1, me1);
        
        x_next = z1_opt(1:m);
        q_next = z1_opt(m+1);
         
        // --- Step 2: Solve LP for (y, p) given x^{s+1} ---
        vec_xAB = x_next' * (A + B);
        c2 = [-vec_xAB'; 1];
        
        // Q matrix for Step 2
        num_vars2 = n + 1; // y (size n) + p (size 1)
        Q2 = zeros(num_vars2, num_vars2);
        
        // Inequality Matrix: [A, -1] * [y; p] <= 0
        C2_ineq = [A, -ones(m, 1)];
        b2_ineq = zeros(m, 1);
        
        // Equality Matrix
        C2_eq = [ones(1, n), 0];
        b2_eq = 1;
        
        // Combine constraints
        C2 = [C2_eq; C2_ineq];
        b2 = [b2_eq; b2_ineq];
        me2 = 1;
        
        // Bounds
        ci2 = [zeros(n, 1); -10000];
        cs2 = [ones(n, 1) * %inf; 10000];
        
        // Solve LP Step 2
        [z2_opt, lagr2, f2_opt] = qpsolve(Q2, c2, C2, b2, ci2, cs2, me2);
        
        y_next = z2_opt(1:n);
        p_next = z2_opt(n+1);
        
        
        // --- Step 3: Check Stopping Criterion ---
        F_curr = (x_next' * (A + B) * y_next) - p_next - q_next;
        F_prev_step = (x_next' * (A + B) * y_curr) - p_curr - q_next; 
        
        diff = abs(F_curr - F_prev_step);
        printf("Iter %d: F = %f, Diff = %f\n", s, F_curr, diff);
        
        if diff <= tol then
            printf("Converged at iteration %d\n", s);
            x_opt = x_next;
            y_opt = y_next;
            p_opt = p_next;
            q_opt = q_next;
            F_val = F_curr;
            return;
        end
        
        // Update variables for next iteration
        y_curr = y_next;
        p_curr = p_next;
        
    end
    
    // If loop finishes without convergence
    printf("Max iterations reached.\n");
    x_opt = x_next;
    y_opt = y_next;
    p_opt = p_next;
    q_opt = q_next;
    F_val = F_curr;
    
endfunction


// --- Example Usage ---
A = [3, 4; -2, 1; 1, 2];
B = [1, 2; 5, 0; 2, -1];
n = size(A, 2);
y0 = ones(n, 1) / n; 
max_iter = 50;
tol = 1e-8;

[x, y, p, q, F] = berge_local_search(A, B, y0, max_iter, tol);

disp("--- Final Results ---");
disp("x* = "); disp(x);
disp("y* = "); disp(y);
disp("p* = "); disp(p);
disp("q* = "); disp(q);
disp("F* = "); disp(F);


