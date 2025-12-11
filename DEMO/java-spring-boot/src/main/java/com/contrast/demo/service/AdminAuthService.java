package com.contrast.demo.service;

import com.contrast.demo.model.AdminUser;
import com.contrast.demo.repository.AdminUserRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.Base64;
import java.util.Optional;

/**
 * Admin Authentication Service - INTENTIONALLY VULNERABLE
 * Uses Base64 encoding instead of proper password hashing
 */
@Service
public class AdminAuthService {
    
    @Autowired
    private AdminUserRepository adminUserRepository;
    
    /**
     * VULNERABILITY: Base64 is NOT encryption, it's encoding
     * Base64 can be easily decoded - this is intentionally insecure
     */
    public String encodePassword(String password) {
        return Base64.getEncoder().encodeToString(password.getBytes());
    }
    
    /**
     * VULNERABILITY: Decodes Base64 "encrypted" password
     */
    public String decodePassword(String encodedPassword) {
        try {
            return new String(Base64.getDecoder().decode(encodedPassword));
        } catch (IllegalArgumentException e) {
            return null;
        }
    }
    
    /**
     * Authenticate admin user with username and encoded password
     * VULNERABILITY: Uses weak Base64 encoding and plain text comparison
     */
    public AdminUser authenticate(String username, String encodedAuth) {
        String decodedPassword = decodePassword(encodedAuth);
        if (decodedPassword == null) {
            return null;
        }
        
        Optional<AdminUser> userOpt = adminUserRepository.findByUsername(username);
        if (userOpt.isPresent()) {
            AdminUser user = userOpt.get();
            // VULNERABILITY: Plain text password comparison
            if (user.getPassword().equals(decodedPassword) && user.isActive()) {
                return user;
            }
        }
        return null;
    }
    
    /**
     * Create new admin user with Base64 encoded password
     */
    public AdminUser createAdmin(String username, String password) {
        AdminUser admin = new AdminUser();
        admin.setUsername(username);
        admin.setPassword(password); // VULNERABILITY: Storing plain text password
        admin.setActive(true);
        admin.setRole("ADMIN");
        return adminUserRepository.save(admin);
    }
    
    /**
     * Get admin user by username
     */
    public Optional<AdminUser> getAdminByUsername(String username) {
        return adminUserRepository.findByUsername(username);
    }
}
