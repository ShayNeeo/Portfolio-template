# Security Policy

## Supported Versions

We actively support and provide security updates for the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 2.x.x   | :white_check_mark: |
| 1.x.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

The Universal Portfolio & Web Tools Platform team takes security seriously. We appreciate your efforts to responsibly disclose your findings.

### How to Report

**For security vulnerabilities, please DO NOT create a public GitHub issue.**

Instead, please report security vulnerabilities by emailing: **shayneeo@pt.io.vn**

Include the following information:
- A description of the vulnerability
- Steps to reproduce the issue
- Potential impact of the vulnerability
- Any suggested fixes (if you have them)

### What to Expect

- **Acknowledgment**: We'll acknowledge receipt of your report within 24 hours
- **Investigation**: We'll investigate and validate the issue within 7 days
- **Updates**: We'll provide regular updates on our progress
- **Resolution**: We aim to resolve critical issues within 30 days
- **Credit**: We'll publicly credit you for the discovery (if you want)

### Security Measures

This platform implements several security measures:

#### Input Validation
- All user inputs are validated and sanitized
- File upload restrictions and validation
- SQL injection prevention through prepared statements
- XSS protection through output escaping

#### File Security
- Uploaded files stored outside web root
- File type validation and restrictions
- Secure file serving mechanisms
- Automatic cleanup of temporary files

#### Access Control
- Directory traversal protection
- Secure configuration file placement
- Private directory access restrictions
- Session security measures

#### Data Protection
- Secure handling of user data
- No sensitive information in logs
- Proper error handling without information disclosure
- HTTPS enforcement recommendations

### Security Best Practices for Users

When deploying this platform:

1. **Use HTTPS**: Always deploy with SSL/TLS certificates
2. **Keep Updated**: Regularly update to the latest version
3. **File Permissions**: Set proper file and directory permissions
4. **Regular Backups**: Implement automated backup strategies
5. **Monitor Logs**: Regularly check error logs for suspicious activity
6. **Environment Variables**: Use environment variables for sensitive configuration
7. **Firewall**: Implement proper firewall rules
8. **Database Security**: Use strong database credentials and restrict access

### Vulnerability Disclosure Timeline

- **Day 0**: Vulnerability reported to shayneeo@pt.io.vn
- **Day 1**: Acknowledgment sent to reporter
- **Day 7**: Vulnerability validated and assessed
- **Day 14**: Fix developed and tested
- **Day 21**: Security update released
- **Day 30**: Public disclosure (if appropriate)

### Security Updates

Security updates are released as:
- **Critical**: Immediate patch release
- **High**: Released within 7 days
- **Medium**: Included in next scheduled release
- **Low**: Included in next major release

### Hall of Fame

We thank the following security researchers for their contributions:

*(No vulnerabilities reported yet - be the first!)*

## Additional Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Guide](https://phpsec.org/)
- [Web Application Security](https://developer.mozilla.org/en-US/docs/Web/Security)

## Contact

For non-security related issues, please use our standard channels:
- GitHub Issues: [github.com/ShayNeeo/Portfolio-template/issues](https://github.com/ShayNeeo/Portfolio-template/issues)
- Discord: [discord.gg/UbtHVJza](https://discord.gg/UbtHVJza)
- Email: [shayneeo@pt.io.vn](mailto:shayneeo@pt.io.vn)
