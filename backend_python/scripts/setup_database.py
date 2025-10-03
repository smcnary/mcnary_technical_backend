"""
Database setup script
"""

import asyncio
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker

from app.core.database import Base
from app.core.config import settings
from app.core.auth import get_password_hash
from app.models.user import User
from app.models.organization import Organization
from app.models.agency import Agency
from app.models.tenant import Tenant

def setup_database():
    """Setup database tables and create default admin user"""
    
    # Create engine
    engine = create_engine(settings.database_url)
    
    # Create all tables
    print("Creating database tables...")
    Base.metadata.create_all(bind=engine)
    print("Database tables created successfully!")
    
    # Create session
    SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
    db = SessionLocal()
    
    try:
        # Check if admin user already exists
        admin_user = db.query(User).filter(User.email == settings.default_admin_email).first()
        if admin_user:
            print(f"Admin user {settings.default_admin_email} already exists!")
            return
        
        # Create default organization
        organization = Organization(name="McNary Technical")
        db.add(organization)
        db.flush()  # Get the ID
        
        # Create default agency
        agency = Agency(name="McNary Technical Agency")
        db.add(agency)
        db.flush()  # Get the ID
        
        # Create default tenant
        tenant = Tenant(name="McNary Technical", slug="mcnary-technical")
        db.add(tenant)
        db.flush()  # Get the ID
        
        # Create admin user
        admin_user = User(
            email=settings.default_admin_email,
            password_hash=get_password_hash(settings.default_admin_password),
            first_name="Sean",
            last_name="McNary",
            status="active",
            role=User.ROLE_SYSTEM_ADMIN,
            organization_id=organization.id,
            agency_id=agency.id,
            tenant_id=tenant.id
        )
        
        db.add(admin_user)
        db.commit()
        
        print(f"Admin user created successfully!")
        print(f"Email: {settings.default_admin_email}")
        print(f"Password: {settings.default_admin_password}")
        print(f"Organization ID: {organization.id}")
        print(f"Agency ID: {agency.id}")
        print(f"Tenant ID: {tenant.id}")
        
    except Exception as e:
        print(f"Error creating admin user: {e}")
        db.rollback()
        raise
    finally:
        db.close()

if __name__ == "__main__":
    setup_database()
