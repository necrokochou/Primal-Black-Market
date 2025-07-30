/**
 * PRIMAL BLACK MARKET ABOUT PAGE
 * Enhanced JavaScript for Premium Brand Experience
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================
    // ABOUT PAGE INITIALIZATION
    // ================================
    
    console.log('📖 Initializing Primal Black Market About Page...');
    
    // ================================
    // INTERSECTION OBSERVER FOR ANIMATIONS
    // ================================
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const animationObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                
                // Add special effects for specific elements
                if (entry.target.classList.contains('about-hero-title')) {
                    typewriterEffect(entry.target);
                }
                
                if (entry.target.classList.contains('about-feature')) {
                    addSparkleEffect(entry.target);
                }
            }
        });
    }, observerOptions);
    
    // Observe all animated elements
    const animatedElements = document.querySelectorAll('.about-section, .about-grid-item, .about-feature, .about-contact, .about-cta');
    animatedElements.forEach(el => animationObserver.observe(el));
    
    // ================================
    // TYPEWRITER EFFECT FOR HERO TITLE
    // ================================
    
    function typewriterEffect(element) {
        const text = element.textContent;
        element.textContent = '';
        element.style.borderRight = '3px solid var(--primal-orange)';
        
        let index = 0;
        const speed = 100;
        
        function type() {
            if (index < text.length) {
                element.textContent += text.charAt(index);
                index++;
                setTimeout(type, speed);
            } else {
                // Remove cursor after typing is complete
                setTimeout(() => {
                    element.style.borderRight = 'none';
                }, 1000);
            }
        }
        
        // Start typing after a short delay
        setTimeout(type, 500);
    }
    
    // ================================
    // SPARKLE EFFECT FOR FEATURES
    // ================================
    
    function addSparkleEffect(element) {
        const sparkles = 5;
        
        for (let i = 0; i < sparkles; i++) {
            setTimeout(() => {
                createSparkle(element);
            }, i * 200);
        }
    }
    
    function createSparkle(container) {
        const sparkle = document.createElement('div');
        sparkle.style.cssText = `
            position: absolute;
            width: 6px;
            height: 6px;
            background: var(--primal-orange);
            border-radius: 50%;
            pointer-events: none;
            z-index: 100;
            animation: sparkleAnimation 1.5s ease-out forwards;
        `;
        
        // Random position within container
        const rect = container.getBoundingClientRect();
        const x = Math.random() * rect.width;
        const y = Math.random() * rect.height;
        
        sparkle.style.left = x + 'px';
        sparkle.style.top = y + 'px';
        
        container.style.position = 'relative';
        container.appendChild(sparkle);
        
        // Remove sparkle after animation
        setTimeout(() => {
            if (container.contains(sparkle)) {
                container.removeChild(sparkle);
            }
        }, 1500);
    }
    
    // ================================
    // ENHANCED HOVER EFFECTS
    // ================================
    
    function initializeHoverEffects() {
        // Enhanced section hover effects
        const sections = document.querySelectorAll('.about-section');
        sections.forEach(section => {
            section.addEventListener('mouseenter', function() {
                createHoverRipple(this);
            });
        });
        
        // Feature card parallax effect
        const features = document.querySelectorAll('.about-feature');
        features.forEach(feature => {
            feature.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                
                this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-5px)`;
            });
            
            feature.addEventListener('mouseleave', function() {
                this.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0px)';
            });
        });
        
        // Grid item glow effect
        const gridItems = document.querySelectorAll('.about-grid-item');
        gridItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 25px 50px rgba(255, 140, 0, 0.2), 0 0 30px rgba(255, 140, 0, 0.1)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.boxShadow = 'var(--about-shadow)';
            });
        });
    }
    
    function createHoverRipple(element) {
        const ripple = document.createElement('div');
        ripple.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 140, 0, 0.1);
            transform: translate(-50%, -50%);
            pointer-events: none;
            animation: rippleExpand 0.8s ease-out;
        `;
        
        element.style.position = 'relative';
        element.appendChild(ripple);
        
        setTimeout(() => {
            if (element.contains(ripple)) {
                element.removeChild(ripple);
            }
        }, 800);
    }
    
    // ================================
    // FLOATING ELEMENTS ANIMATION
    // ================================
    
    function initializeFloatingElements() {
        const floatingElements = document.querySelectorAll('.about-grid-icon, .about-feature-icon');
        
        floatingElements.forEach((element, index) => {
            // Add subtle floating animation
            element.style.animation = `floatAnimation ${3 + index * 0.5}s ease-in-out infinite`;
            element.style.animationDelay = `${index * 0.2}s`;
        });
    }
    
    // ================================
    // SCROLL PROGRESS INDICATOR
    // ================================
    
    function initializeScrollProgress() {
        const progressBar = document.createElement('div');
        progressBar.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: linear-gradient(90deg, var(--primal-orange), var(--primal-brown));
            z-index: 9999;
            transition: width 0.1s ease-out;
            box-shadow: 0 2px 10px rgba(255, 140, 0, 0.3);
        `;
        
        document.body.appendChild(progressBar);
        
        function updateScrollProgress() {
            const scrollTop = window.pageYOffset;
            const docHeight = document.body.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;
            
            progressBar.style.width = scrollPercent + '%';
        }
        
        window.addEventListener('scroll', updateScrollProgress);
        updateScrollProgress();
    }
    
    // ================================
    // CONTACT FORM ENHANCEMENT
    // ================================
    
    function initializeContactInteractions() {
        const contactItems = document.querySelectorAll('.about-contact-item');
        
        contactItems.forEach(item => {
            item.addEventListener('click', function() {
                const link = this.querySelector('a');
                if (link) {
                    // Add click animation
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                    
                    // Show feedback
                    showContactFeedback(link.textContent);
                }
            });
        });
    }
    
    function showContactFeedback(contactInfo) {
        const feedback = document.createElement('div');
        feedback.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: linear-gradient(135deg, var(--primal-green), #28a745);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        `;
        
        feedback.innerHTML = `
            <i class="fas fa-copy" style="margin-right: 0.5rem;"></i>
            Contact info ready to copy!
        `;
        
        document.body.appendChild(feedback);
        
        // Animate in
        setTimeout(() => {
            feedback.style.transform = 'translateX(0)';
        }, 100);
        
        // Animate out
        setTimeout(() => {
            feedback.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(feedback)) {
                    document.body.removeChild(feedback);
                }
            }, 400);
        }, 3000);
    }
    
    // ================================
    // STATISTICS COUNTER ANIMATION
    // ================================
    
    function initializeCounters() {
        const counters = document.querySelectorAll('.counter');
        
        counters.forEach(counter => {
            const target = parseInt(counter.dataset.target);
            const duration = 2000; // 2 seconds
            const increment = target / (duration / 16); // 60fps
            let current = 0;
            
            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.ceil(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            // Start counter when element is visible
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        updateCounter();
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            observer.observe(counter);
        });
    }
    
    // ================================
    // DYNAMIC BACKGROUND PARTICLES
    // ================================
    
    function initializeParticles() {
        const particleCount = 20;
        const particles = [];
        
        for (let i = 0; i < particleCount; i++) {
            createParticle();
        }
        
        function createParticle() {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: fixed;
                width: 2px;
                height: 2px;
                background: rgba(255, 140, 0, 0.3);
                border-radius: 50%;
                pointer-events: none;
                z-index: 1;
                animation: particleFloat ${10 + Math.random() * 20}s linear infinite;
            `;
            
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = '110%';
            particle.style.animationDelay = Math.random() * 10 + 's';
            
            document.body.appendChild(particle);
            particles.push(particle);
        }
    }
    
    // ================================
    // SMOOTH SCROLLING FOR ANCHOR LINKS
    // ================================
    
    function initializeSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Add highlight effect
                    targetElement.style.boxShadow = '0 0 30px rgba(255, 140, 0, 0.3)';
                    setTimeout(() => {
                        targetElement.style.boxShadow = 'var(--about-shadow)';
                    }, 2000);
                }
            });
        });
    }
    
    // ================================
    // INITIALIZATION
    // ================================
    
    // Initialize all features
    initializeHoverEffects();
    initializeFloatingElements();
    initializeScrollProgress();
    initializeContactInteractions();
    initializeCounters();
    initializeParticles();
    initializeSmoothScrolling();
    
    // Add loading animation
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.opacity = '1';
        document.body.style.transition = 'opacity 0.8s ease-out';
    }, 100);
    
    console.log('✅ Primal Black Market About Page initialized successfully!');
});

// ================================
// CSS KEYFRAMES FOR ANIMATIONS
// ================================

const aboutStyles = document.createElement('style');
aboutStyles.textContent = `
    @keyframes sparkleAnimation {
        0% {
            transform: scale(0) rotate(0deg);
            opacity: 1;
        }
        50% {
            transform: scale(1) rotate(180deg);
            opacity: 0.8;
        }
        100% {
            transform: scale(0) rotate(360deg);
            opacity: 0;
        }
    }
    
    @keyframes rippleExpand {
        0% {
            width: 0;
            height: 0;
            opacity: 0.5;
        }
        100% {
            width: 200px;
            height: 200px;
            opacity: 0;
        }
    }
    
    @keyframes floatAnimation {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }
    
    @keyframes particleFloat {
        0% {
            transform: translateY(0px) translateX(0px);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-100vh) translateX(${Math.random() * 100 - 50}px);
            opacity: 0;
        }
    }
    
    @keyframes teamParticleFloat {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 0.7;
        }
        25% {
            transform: translateY(-20px) rotate(90deg);
            opacity: 1;
        }
        50% {
            transform: translateY(-10px) rotate(180deg);
            opacity: 0.8;
        }
        75% {
            transform: translateY(-30px) rotate(270deg);
            opacity: 1;
        }
        100% {
            transform: translateY(0) rotate(360deg);
            opacity: 0.7;
        }
    }
    
    @keyframes advancedRipple {
        0% {
            transform: translate(-50%, -50%) scale(0);
            opacity: 0.8;
        }
        50% {
            opacity: 0.4;
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 0;
        }
    }
    
    @keyframes expandClick {
        0% {
            transform: translate(-50%, -50%) scale(0);
            opacity: 1;
        }
        100% {
            transform: translate(-50%, -50%) scale(4);
            opacity: 0;
        }
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 140, 0, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(255, 140, 0, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 140, 0, 0);
        }
    }
    
    .animate-in {
        animation-play-state: running !important;
    }
    
    .counter {
        font-weight: 700;
        color: var(--primal-orange);
        font-size: 2rem;
    }
`;

    // ================================
    // ENHANCED TEAM MEMBER INTERACTIONS
    // ================================
    
    function initializeEnhancedTeamAnimations() {
        const teamMembers = document.querySelectorAll('.team-member');
        
        if (teamMembers.length === 0) return;
        
        // Enhanced intersection observer for team members
        const teamObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('animate-in');
                        entry.target.style.animationDelay = `${index * 0.15}s`;
                        
                        // Add special entrance effect
                        entry.target.style.transform = 'translateY(0) scale(1) rotateY(0deg)';
                        entry.target.style.opacity = '1';
                        
                        // Create floating particles around the card
                        createTeamMemberParticles(entry.target);
                        
                    }, index * 150);
                }
            });
        }, { threshold: 0.2 });
        
        teamMembers.forEach(member => {
            teamObserver.observe(member);
            
            // Enhanced hover interactions
            member.addEventListener('mouseenter', function() {
                // 3D transform effect
                this.style.transform = 'translateY(-20px) rotateX(5deg) rotateY(2deg) scale(1.03)';
                this.style.zIndex = '10';
                
                // Create ripple effect
                createAdvancedRipple(this);
                
                // Animate image with advanced effects
                const image = this.querySelector('.team-member-image');
                if (image) {
                    image.style.transform = 'scale(1.15) rotate(3deg)';
                    image.style.filter = 'brightness(1.2) contrast(1.1) saturate(1.2)';
                }
                
                // Enhanced social link animations
                const socialLinks = this.querySelectorAll('.team-social-link');
                socialLinks.forEach((link, i) => {
                    setTimeout(() => {
                        link.style.transform = 'scale(1.2) rotate(10deg) translateY(-2px)';
                        link.style.boxShadow = '0 8px 25px rgba(255, 140, 0, 0.4)';
                    }, i * 80);
                });
                
                // Animate text content
                const name = this.querySelector('.team-member-name');
                const role = this.querySelector('.team-member-role');
                const description = this.querySelector('.team-member-description');
                
                if (name) name.style.transform = 'translateY(-3px)';
                if (role) role.style.transform = 'translateY(-2px)';
                if (description) description.style.transform = 'translateY(-1px)';
                
                // Add enhanced glow effect
                this.style.boxShadow = `
                    0 35px 80px rgba(0, 0, 0, 0.7),
                    0 0 40px rgba(255, 140, 0, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2)
                `;
            });
            
            member.addEventListener('mouseleave', function() {
                // Reset transforms
                this.style.transform = 'translateY(0) rotateX(0deg) rotateY(0deg) scale(1)';
                this.style.zIndex = '1';
                
                // Reset image
                const image = this.querySelector('.team-member-image');
                if (image) {
                    image.style.transform = 'scale(1) rotate(0deg)';
                    image.style.filter = 'brightness(1) contrast(1) saturate(1)';
                }
                
                // Reset social links
                const socialLinks = this.querySelectorAll('.team-social-link');
                socialLinks.forEach(link => {
                    link.style.transform = 'scale(1) rotate(0deg) translateY(0)';
                    link.style.boxShadow = '0 5px 15px rgba(255, 255, 255, 0.3)';
                });
                
                // Reset text
                const name = this.querySelector('.team-member-name');
                const role = this.querySelector('.team-member-role');
                const description = this.querySelector('.team-member-description');
                
                if (name) name.style.transform = 'translateY(0)';
                if (role) role.style.transform = 'translateY(0)';
                if (description) description.style.transform = 'translateY(0)';
                
                // Reset glow
                this.style.boxShadow = `
                    0 20px 40px rgba(0, 0, 0, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1)
                `;
            });
            
            // Enhanced social link interactions
            const socialLinks = member.querySelectorAll('.team-social-link');
            socialLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.4) rotate(360deg) translateY(-3px)';
                    this.style.background = 'linear-gradient(135deg, white, #f0f0f0)';
                    this.style.color = 'var(--primal-orange)';
                    this.style.boxShadow = '0 10px 30px rgba(255, 140, 0, 0.5)';
                    
                    // Add pulse effect
                    this.style.animation = 'pulse 0.6s ease-in-out';
                });
                
                link.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1) rotate(0deg) translateY(0)';
                    this.style.background = 'rgba(255, 255, 255, 0.15)';
                    this.style.color = 'white';
                    this.style.boxShadow = '0 5px 15px rgba(255, 255, 255, 0.3)';
                    this.style.animation = 'none';
                });
                
                // Click effect
                link.addEventListener('click', function(e) {
                    // Create expanding circle effect
                    const circle = document.createElement('div');
                    circle.style.cssText = `
                        position: absolute;
                        width: 20px;
                        height: 20px;
                        background: radial-gradient(circle, rgba(255, 140, 0, 0.8), transparent);
                        border-radius: 50%;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%) scale(0);
                        pointer-events: none;
                        z-index: 100;
                        animation: expandClick 0.6s ease-out forwards;
                    `;
                    
                    this.appendChild(circle);
                    
                    setTimeout(() => {
                        if (circle.parentNode) {
                            circle.parentNode.removeChild(circle);
                        }
                    }, 600);
                });
            });
            
            // Add card tilt effect based on mouse position
            member.addEventListener('mousemove', function(e) {
                if (window.innerWidth <= 768) return; // Disable on mobile
                
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / centerY * -8;
                const rotateY = (x - centerX) / centerX * 8;
                
                this.style.transform = `
                    translateY(-20px) 
                    rotateX(${rotateX}deg) 
                    rotateY(${rotateY}deg) 
                    scale(1.03)
                `;
            });
            
            member.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) rotateX(0deg) rotateY(0deg) scale(1)';
            });
        });
    }
    
    // Create floating particles around team member cards
    function createTeamMemberParticles(memberElement) {
        const particleCount = 6;
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.classList.add('team-particle');
            
            const size = Math.random() * 4 + 2;
            const duration = Math.random() * 3 + 2;
            const delay = Math.random() * 2;
            
            particle.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                background: radial-gradient(circle, rgba(255, 140, 0, 0.8), rgba(255, 140, 0, 0.2));
                border-radius: 50%;
                top: ${Math.random() * 100}%;
                left: ${Math.random() * 100}%;
                pointer-events: none;
                z-index: 0;
                animation: teamParticleFloat ${duration}s ease-in-out infinite;
                animation-delay: ${delay}s;
                opacity: 0;
            `;
            
            memberElement.appendChild(particle);
            
            // Animate particle in
            setTimeout(() => {
                particle.style.opacity = '1';
            }, delay * 1000);
            
            // Remove particles after some time to prevent accumulation
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.parentNode.removeChild(particle);
                }
            }, 10000);
        }
    }
    
    // Advanced ripple effect
    function createAdvancedRipple(element) {
        const ripple = document.createElement('div');
        ripple.classList.add('advanced-ripple');
        
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height) * 1.5;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            background: radial-gradient(circle, 
                rgba(255, 140, 0, 0.3) 0%, 
                rgba(255, 140, 0, 0.1) 30%, 
                transparent 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            pointer-events: none;
            z-index: 1;
            animation: advancedRipple 1.2s ease-out forwards;
        `;
        
        element.appendChild(ripple);
        
        setTimeout(() => {
            if (ripple.parentNode) {
                ripple.parentNode.removeChild(ripple);
            }
        }, 1200);
    }
    
    // Initialize enhanced team animations
    initializeEnhancedTeamAnimations();

document.head.appendChild(aboutStyles);
