FROM mysql:8.0

# Add custom configuration
COPY my.cnf /etc/mysql/conf.d/my.cnf

# Health check to ensure MySQL is fully operational
HEALTHCHECK --interval=30s --timeout=10s --retries=3 \
  CMD mysqladmin ping -h localhost -u root -p${MYSQL_ROOT_PASSWORD} || exit 1