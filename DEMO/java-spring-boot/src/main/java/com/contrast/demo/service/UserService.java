package com.contrast.demo.service;

import com.contrast.demo.model.User;
import com.contrast.demo.repository.UserRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.InputStreamReader;
import java.util.List;

@Service
public class UserService {

    @Autowired
    private UserRepository userRepository;

    public List<User> getAllUsers() {
        return userRepository.findAll();
    }

    public User getUserById(Long id) {
        return userRepository.findById(id).orElse(null);
    }

    public User saveUser(User user) {
        return userRepository.save(user);
    }

    public void deleteUser(Long id) {
        userRepository.deleteById(id);
    }

    // Intentionally vulnerable search method for security testing
    public List<User> searchUsers(String query) {
        try {
            // This uses the vulnerable native query in the repository
            return userRepository.findByQuery(query);
        } catch (Exception e) {
            // Fallback to safe search
            return userRepository.findByNameContainingIgnoreCase(query);
        }
    }

    // Intentionally vulnerable file reading method for security testing
    public String readFile(String filename) {
        try {
            // This is intentionally vulnerable to path traversal
            File file = new File(filename);
            if (!file.exists()) {
                return "File not found: " + filename;
            }
            
            StringBuilder content = new StringBuilder();
            try (BufferedReader reader = new BufferedReader(new FileReader(file))) {
                String line;
                while ((line = reader.readLine()) != null) {
                    content.append(line).append("\n");
                }
            }
            return content.toString();
        } catch (Exception e) {
            return "Error reading file: " + e.getMessage();
        }
    }

    // Intentionally vulnerable command execution method for security testing
    public String executeCommand(String command) {
        try {
            // This is intentionally vulnerable to command injection
            Process process = Runtime.getRuntime().exec(command);
            StringBuilder output = new StringBuilder();
            
            try (BufferedReader reader = new BufferedReader(new InputStreamReader(process.getInputStream()))) {
                String line;
                while ((line = reader.readLine()) != null) {
                    output.append(line).append("\n");
                }
            }
            
            try (BufferedReader errorReader = new BufferedReader(new InputStreamReader(process.getErrorStream()))) {
                String line;
                while ((line = errorReader.readLine()) != null) {
                    output.append("ERROR: ").append(line).append("\n");
                }
            }
            
            return output.toString();
        } catch (Exception e) {
            return "Error executing command: " + e.getMessage();
        }
    }
}
