package com.contrast.demo.filter;

import com.contrast.demo.model.AdminUser;
import com.contrast.demo.service.AdminAuthService;
import org.springframework.beans.factory.annotation.Autowired;

import javax.servlet.*;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.io.IOException;

/**
 * Admin Authentication Filter - INTENTIONALLY VULNERABLE
 * Checks for authentication headers on admin endpoints
 * Demonstrates insecure authentication via custom headers
 */
public class AdminAuthFilter implements Filter {
    
    @Autowired
    private AdminAuthService adminAuthService;
    
    @Override
    public void init(FilterConfig filterConfig) throws ServletException {
        // Initialization logic if needed
    }
    
    @Override
    public void doFilter(ServletRequest request, ServletResponse response, FilterChain chain)
            throws IOException, ServletException {
        
        HttpServletRequest httpRequest = (HttpServletRequest) request;
        HttpServletResponse httpResponse = (HttpServletResponse) response;
        String path = httpRequest.getRequestURI();
        
        // Only filter admin endpoints (except login page)
        if (path.startsWith("/admin") && !path.equals("/admin/login")) {
            
            // VULNERABILITY: Authentication via custom headers (can be easily spoofed)
            String username = httpRequest.getHeader("X-Admin-Username");
            String encodedAuth = httpRequest.getHeader("X-Admin-Auth");
            
            HttpSession session = httpRequest.getSession(false);
            AdminUser authenticatedUser = null;
            
            // Check session first
            if (session != null) {
                authenticatedUser = (AdminUser) session.getAttribute("adminUser");
            }
            
            // If not in session, try header authentication
            if (authenticatedUser == null && username != null && encodedAuth != null) {
                authenticatedUser = adminAuthService.authenticate(username, encodedAuth);
                if (authenticatedUser != null) {
                    // Store in session
                    HttpSession newSession = httpRequest.getSession(true);
                    newSession.setAttribute("adminUser", authenticatedUser);
                }
            }
            
            // If still not authenticated, redirect to login
            if (authenticatedUser == null) {
                httpResponse.sendRedirect(httpRequest.getContextPath() + "/admin/login");
                return;
            }
        }
        
        chain.doFilter(request, response);
    }
    
    @Override
    public void destroy() {
        // Cleanup logic if needed
    }
}
