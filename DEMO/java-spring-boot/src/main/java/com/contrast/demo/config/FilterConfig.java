package com.contrast.demo.config;

import com.contrast.demo.filter.AdminAuthFilter;
import com.contrast.demo.service.AdminAuthService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.web.servlet.FilterRegistrationBean;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

/**
 * Filter configuration for admin authentication
 */
@Configuration
public class FilterConfig {
    
    @Autowired
    private AdminAuthService adminAuthService;
    
    @Bean
    public FilterRegistrationBean<AdminAuthFilter> adminAuthFilter() {
        FilterRegistrationBean<AdminAuthFilter> registrationBean = new FilterRegistrationBean<>();
        AdminAuthFilter filter = new AdminAuthFilter();
        filter.setAdminAuthService(adminAuthService);
        registrationBean.setFilter(filter);
        registrationBean.addUrlPatterns("/admin/*");
        registrationBean.setOrder(1);
        return registrationBean;
    }
}
