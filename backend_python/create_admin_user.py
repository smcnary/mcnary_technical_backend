"""
Script to create the default system admin user matching the Symfony setup
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from sqlalchemy.orm import sessionmaker
from app.core.database import engine
from app.core.auth import get_password_hash
from app.models.user import User, UserStatus
from app.models.organization import Organization
from app.models.agency import Agency
from app.models.tenant import Tenant
import uuid

def create_admin_user():
    """Create the default system admin user"""
    
    # Create session
    Session = sessionmaker(bind=engine)
    db = Session()
    
    try:
        # Create default organization if it doesn't exist
        org = db.query(Organization).filter(Organization.id == "00000000-0000-0000-0000-000000000001").first()
        if not org:
            org = Organization(
                id=uuid.UUID("00000000-0000-0000-0000-000000000001"),
                name="Default Organization"
            )
            db.add(org)
            print("Created default organization")
        
        # Create default agency if it doesn't exist
        agency = db.query(Agency).filter(Agency.name == "Default Agency").first()
        if not agency:
            agency = Agency(
                name="Default Agency",
                slug="default-agency"
            )
            db.add(agency)
            print("Created default agency")
        
        # Create default tenant if it doesn't exist
        tenant = db.query(Tenant).filter(Tenant.name == "Default Tenant").first()
        if not tenant:
            tenant = Tenant(
                name="Default Tenant",
                slug="default-tenant"
            )
            db.add(tenant)
            print("Created default tenant")
        
        # Commit organization, agency, and tenant
        db.commit()
        
        # Check if admin user already exists
        admin_user = db.query(User).filter(User.email == "smcnary@live.com").first()
        if admin_user:
            print("Admin user already exists")
            return admin_user
        
        # Create admin user
        admin_user = User(
            id=uuid.UUID("0199a704-7312-74f1-91b6-bb96b15b3ed0"),  # Same ID as Symfony
            email="smcnary@live.com",
            password_hash=get_password_hash("TulsaSEO122"),
            first_name="Sean",
            last_name="McNary",
            status=UserStatus.ACTIVE,
            organization_id=org.id,
            agency_id=agency.id,
            tenant_id=tenant.id,
            last_login_at=None,
            metadata_json=None
        )
        
        db.add(admin_user)
        db.commit()
        db.refresh(admin_user)
        
        print(f"Created admin user: {admin_user.email}")
        print(f"User ID: {admin_user.id}")
        print(f"Status: {admin_user.status.value}")
        
        return admin_user
        
    except Exception as e:
        db.rollback()
        print(f"Error creating admin user: {e}")
        raise
    finally:
        db.close()

if __name__ == "__main__":
    create_admin_user()
