#!/bin/bash

# AWS EC2 Instance Connection Helper
# This script helps you find the correct connection details for your EC2 instance

echo "🔍 AWS EC2 Instance Connection Helper"
echo "====================================="
echo

# Check if AWS CLI is installed
if ! command -v aws &> /dev/null; then
    echo "❌ AWS CLI is not installed!"
    echo "💡 Install it with: brew install awscli"
    echo "💡 Or download from: https://aws.amazon.com/cli/"
    exit 1
fi

echo "✅ AWS CLI found"
echo

# Check AWS credentials
if ! aws sts get-caller-identity &> /dev/null; then
    echo "❌ AWS credentials not configured!"
    echo "💡 Run: aws configure"
    echo "💡 Or set environment variables: AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY"
    exit 1
fi

echo "✅ AWS credentials configured"
echo

# List EC2 instances
echo "📋 Available EC2 Instances:"
echo "---------------------------"
aws ec2 describe-instances \
    --query 'Reservations[*].Instances[*].[InstanceId,State.Name,PublicIpAddress,PrivateIpAddress,Tags[?Key==`Name`].Value|[0],InstanceType]' \
    --output table

echo
echo "🔍 Connection Details for Each Instance:"
echo "========================================"

# Get detailed connection info
aws ec2 describe-instances \
    --query 'Reservations[*].Instances[*].[InstanceId,State.Name,PublicIpAddress,PrivateIpAddress,Tags[?Key==`Name`].Value|[0],InstanceType,KeyName,SecurityGroups[0].GroupName]' \
    --output table

echo
echo "📝 Manual Connection Steps:"
echo "==========================="
echo "1. Go to AWS EC2 Console"
echo "2. Select your staging instance"
echo "3. Click 'Connect' button"
echo "4. Choose 'EC2 Instance Connect' or 'SSH Client'"
echo "5. Follow the connection instructions"
echo
echo "🔑 To add the public key:"
echo "1. Connect to your instance using AWS Console"
echo "2. Run these commands:"
echo "   mkdir -p ~/.ssh"
echo "   echo 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIMyp17+lbx1A5qWRxJZMozx9D9HHBa7Xo54A2gokG0yS github-actions-staging' >> ~/.ssh/authorized_keys"
echo "   chmod 700 ~/.ssh"
echo "   chmod 600 ~/.ssh/authorized_keys"
echo
echo "🧪 Test connection:"
echo "   ssh -i staging_key ubuntu@YOUR_PUBLIC_IP"
echo "   # or"
echo "   ssh -i staging_key ec2-user@YOUR_PUBLIC_IP"
