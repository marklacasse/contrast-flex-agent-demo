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
        
        System.out.println("AdminAuthFilter: Processing path: " + path + " (URI: " + httpRequest.getRequestURI() + ")");
        
        // Allow login page
        if (path.endsWith("/admin/login")) {
            System.out.println("AdminAuthFilter: Allowing login page");
            chain.doFilter(request, response);
            return;
        }
        
        // Check if service was properly injected
        if (adminAuthService == null) {
            System.err.println("ERROR: AdminAuthService is null in AdminAuthFilter!");
            httpResponse.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR, 
                "Authentication service not initialized");
            return;
        }
        
        System.out.println("AdminAuthFilter: Checking authentication");
        
        // VULNERABILITY: Authentication via custom headers (can be easily spoofed)
        String username = httpRequest.getHeader("X-Admin-Username");
        String encodedAuth = httpRequest.getHeader("X-Admin-Auth");
        
        HttpSession session = httpRequest.getSession(false);
        AdminUser authenticatedUser = null;
        
        // Check session first
        if (session != null) {
            authenticatedUser = (AdminUser) session.getAttribute("adminUser");
            System.out.println("AdminAuthFilter: Session auth user: " + (authenticatedUser != null ? authenticatedUser.getUsername() : "null"));
        }
        
        // If not in session, try header authentication
        if (authenticatedUser == null && username != null && encodedAuth != null) {
            System.out.println("AdminAuthFilter: Trying header auth for user: " + username);
            authenticatedUser = adminAuthService.authenticate(username, encodedAuth);
            if (authenticatedUser != null) {
                // Store in session
                HttpSession newSession = httpRequest.getSession(true);
                newSession.setAttribute("adminUser", authenticatedUser);
                System.out.println("AdminAuthFilter: Header auth successful, stored in session");
            }
        }
        
        // If still not authenticated, redirect to login
        if (authenticatedUser == null) {
            System.out.println("AdminAuthFilter: Not authenticated, redirecting to login");
            String loginUrl = httpRequest.getContextPath() + "/admin/login";
            httpResponse.sendRedirect(httpResponse.encodeRedirectURL(loginUrl));
            return;
        }
        
        System.out.println("AdminAuthFilter: Authentication successful, proceeding");
        chain.doFilter(request, response);
    }
    
    @Override
    public void destroy() {
        // Cleanup logic if needed
    }
}
