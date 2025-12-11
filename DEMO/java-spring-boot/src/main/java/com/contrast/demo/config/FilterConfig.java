package com.contrast.demo.config;

import com.contrast.demo.filter.AdminAuthFilter;
import org.springframework.boot.web.servlet.FilterRegistrationBean;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

/**
 * Filter configuration for admin authentication
 */
@Configuration
public class FilterConfig {
    
    @Bean
    public FilterRegistrationBean<AdminAuthFilter> adminAuthFilter(AdminAuthFilter filter) {
        FilterRegistrationBean<AdminAuthFilter> registrationBean = new FilterRegistrationBean<>();
        registrationBean.setFilter(filter);
        registrationBean.addUrlPatterns("/admin/*");
        registrationBean.setOrder(1);
        return registrationBean;
    }
}
