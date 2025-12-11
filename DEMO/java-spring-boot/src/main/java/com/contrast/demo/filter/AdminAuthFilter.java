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
    
    private AdminAuthService adminAuthService;
    
    public void setAdminAuthService(AdminAuthService adminAuthService) {
        this.adminAuthService = adminAuthService;
    }
    
    @Override
    public void init(FilterConfig filterConfig) throws ServletException {
        // Initialization logic if needed
    }
    
    @Override
    public void doFilter(ServletRequest request, ServletResponse response, FilterChain chain)
            throws IOException, ServletException {
        
        HttpServletRequest httpRequest = (HttpServletRequest) request;
        HttpServletResponse httpResponse = (HttpServletResponse) response;
        String path = httpRequest.getServletPath();
        
        // Allow login page
        if (path.endsWith("/admin/login")) {
            chain.doFilter(request, response);
            return;
        }
        
        // Check if service was properly injected
        if (adminAuthService == null) {
            httpResponse.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR, 
                "Authentication service not initialized");
            return;
        }
        
        // VULNERABILITY: Authentication via custom headers (can be easily spoofed)
        // Headers must be provided on EVERY request - no session persistence
        String username = httpRequest.getHeader("X-Admin-Username");
        String encodedAuth = httpRequest.getHeader("X-Admin-Auth");
        
        // Require headers on every request
        if (username == null || encodedAuth == null) {
            String loginUrl = httpRequest.getContextPath() + "/admin/login";
            httpResponse.sendRedirect(httpResponse.encodeRedirectURL(loginUrl));
            return;
        }
        
        // Authenticate using headers
        AdminUser authenticatedUser = adminAuthService.authenticate(username, encodedAuth);
        
        // If authentication failed, redirect to login
        if (authenticatedUser == null) {
            String loginUrl = httpRequest.getContextPath() + "/admin/login";
            httpResponse.sendRedirect(httpResponse.encodeRedirectURL(loginUrl));
            return;
        }
        
        // Store user in request attribute (not session) for this request only
        httpRequest.setAttribute("adminUser", authenticatedUser);
        
        chain.doFilter(request, response);
    }
    
    @Override
    public void destroy() {
        // Cleanup logic if needed
    }
}
