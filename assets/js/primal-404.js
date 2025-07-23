/**
 * PRIMAL BLACK MARKET - 404 ERROR PAGE FUNCTIONALITY
 * Enhanced 404 page with primal theme integration
 */

document.addEventListener('DOMContentLoaded', function() {
    initialize404Page();
});

function initialize404Page() {
    setupAnimations();
    setupSearchFunctionality();
    setupNavigationTracking();
    setupEasterEggs();
    console.log('💀 Primal 404 Page initialized - Lost in the void...');
}

// Setup entrance animations with staggered delays
function setupAnimations() {
    const sections = document.querySelectorAll('.error-hero, .error-message, .error-navigation, .error-search, .error-categories, .error-support');
    
    // Intersection Observer for scroll-triggered animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        section.style.transition = 'all 0.6s ease-out';
        section.style.transitionDelay = `${index * 0.2}s`;
        observer.observe(section);
    });

    // Special animations for error number
    animateErrorNumber();
}

// Animate the 404 number with individual letter effects
function animateErrorNumber() {
    const numbers = document.querySelectorAll('.error-number .four, .error-number .zero');
    
    numbers.forEach((number, index) => {
        number.addEventListener('mouseenter', () => {
            number.style.transform = 'scale(1.1) rotate(5deg)';
            number.style.transition = 'transform 0.3s ease';
        });
        
        number.addEventListener('mouseleave', () => {
            number.style.transform = 'scale(1) rotate(0deg)';
        });
        
        // Add random glitch effect
        setInterval(() => {
            if (Math.random() < 0.1) { // 10% chance every interval
                glitchEffect(number);
            }
        }, 3000 + (index * 1000));
    });
}

// Glitch effect for error numbers
function glitchEffect(element) {
    const originalText = element.textContent;
    const glitchChars = ['4', '0', '?', '#', '@', '%'];
    
    element.style.color = '#dc3545';
    element.textContent = glitchChars[Math.floor(Math.random() * glitchChars.length)];
    
    setTimeout(() => {
        element.style.color = '#ff8c00';
        element.textContent = originalText;
    }, 150);
}

// Enhanced search functionality
function setupSearchFunctionality() {
    const searchForm = document.querySelector('.search-form');
    const searchInput = document.querySelector('.search-input');
    const searchButton = document.querySelector('.search-button');
    
    if (!searchForm || !searchInput || !searchButton) return;
    
    // Enhanced search with suggestions
    let searchSuggestions = [
        'weapons', 'armor', 'potions', 'artifacts', 'materials', 'pets',
        'rare items', 'legendary gear', 'primal artifacts', 'ancient relics',
        'mystical items', 'enchanted gear', 'forbidden items'
    ];
    
    // Add autocomplete suggestions
    setupAutocomplete(searchInput, searchSuggestions);
    
    // Search form submission
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const searchTerm = searchInput.value.trim();
        
        if (searchTerm) {
            showSearchFeedback();
            // Redirect to shop with search term
            setTimeout(() => {
                window.location.href = `/pages/shop?search=${encodeURIComponent(searchTerm)}`;
            }, 1000);
        }
    });
    
    // Search button hover effects
    searchButton.addEventListener('mouseenter', () => {
        searchButton.innerHTML = '<i class="fas fa-crosshairs"></i> Hunt';
    });
    
    searchButton.addEventListener('mouseleave', () => {
        searchButton.innerHTML = '<i class="fas fa-search"></i> Hunt';
    });
}

// Simple autocomplete functionality
function setupAutocomplete(input, suggestions) {
    let currentSuggestionIndex = -1;
    let suggestionsList = null;
    
    input.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        currentSuggestionIndex = -1;
        
        // Remove existing suggestions
        if (suggestionsList) {
            suggestionsList.remove();
            suggestionsList = null;
        }
        
        if (value.length < 2) return;
        
        // Filter suggestions
        const matches = suggestions.filter(suggestion => 
            suggestion.toLowerCase().includes(value)
        ).slice(0, 5);
        
        if (matches.length === 0) return;
        
        // Create suggestions dropdown
        suggestionsList = document.createElement('div');
        suggestionsList.className = 'search-suggestions';
        suggestionsList.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(30, 30, 30, 0.95);
            border: 1px solid rgba(255, 140, 0, 0.3);
            border-radius: 0 0 10px 10px;
            border-top: none;
            backdrop-filter: blur(20px);
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
        `;
        
        matches.forEach((suggestion, index) => {
            const item = document.createElement('div');
            item.className = 'suggestion-item';
            item.textContent = suggestion;
            item.style.cssText = `
                padding: 0.8rem 1rem;
                color: #e0e0e0;
                cursor: pointer;
                border-bottom: 1px solid rgba(255, 140, 0, 0.1);
                transition: background-color 0.2s ease;
            `;
            
            item.addEventListener('mouseenter', () => {
                item.style.backgroundColor = 'rgba(255, 140, 0, 0.1)';
                item.style.color = '#ff8c00';
            });
            
            item.addEventListener('mouseleave', () => {
                item.style.backgroundColor = 'transparent';
                item.style.color = '#e0e0e0';
            });
            
            item.addEventListener('click', () => {
                input.value = suggestion;
                suggestionsList.remove();
                suggestionsList = null;
            });
            
            suggestionsList.appendChild(item);
        });
        
        // Position suggestions relative to input
        const inputContainer = input.parentElement;
        inputContainer.style.position = 'relative';
        inputContainer.appendChild(suggestionsList);
    });
    
    // Handle keyboard navigation
    input.addEventListener('keydown', function(e) {
        if (!suggestionsList) return;
        
        const items = suggestionsList.querySelectorAll('.suggestion-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentSuggestionIndex = Math.min(currentSuggestionIndex + 1, items.length - 1);
            highlightSuggestion(items, currentSuggestionIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentSuggestionIndex = Math.max(currentSuggestionIndex - 1, -1);
            highlightSuggestion(items, currentSuggestionIndex);
        } else if (e.key === 'Enter' && currentSuggestionIndex >= 0) {
            e.preventDefault();
            items[currentSuggestionIndex].click();
        } else if (e.key === 'Escape') {
            suggestionsList.remove();
            suggestionsList = null;
        }
    });
    
    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (suggestionsList && !input.contains(e.target) && !suggestionsList.contains(e.target)) {
            suggestionsList.remove();
            suggestionsList = null;
        }
    });
}

function highlightSuggestion(items, index) {
    items.forEach((item, i) => {
        if (i === index) {
            item.style.backgroundColor = 'rgba(255, 140, 0, 0.2)';
            item.style.color = '#ff8c00';
        } else {
            item.style.backgroundColor = 'transparent';
            item.style.color = '#e0e0e0';
        }
    });
}

function showSearchFeedback() {
    const searchButton = document.querySelector('.search-button');
    const originalContent = searchButton.innerHTML;
    
    searchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Hunting...';
    searchButton.disabled = true;
    
    setTimeout(() => {
        searchButton.innerHTML = originalContent;
        searchButton.disabled = false;
    }, 1000);
}

// Track navigation patterns for analytics
function setupNavigationTracking() {
    const navOptions = document.querySelectorAll('.nav-option');
    const categoryItems = document.querySelectorAll('.category-item');
    
    navOptions.forEach(option => {
        option.addEventListener('click', function() {
            const destination = this.href;
            console.log(`Navigation: User clicked ${destination} from 404 page`);
            
            // Add click animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    categoryItems.forEach(item => {
        item.addEventListener('click', function() {
            const category = this.querySelector('span').textContent;
            console.log(`Category: User clicked ${category} from 404 page`);
            
            // Add click animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
}

// Easter eggs and hidden features
function setupEasterEggs() {
    let konami = [];
    const konamiCode = [
        'ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown',
        'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight',
        'KeyB', 'KeyA'
    ];
    
    document.addEventListener('keydown', function(e) {
        konami.push(e.code);
        konami = konami.slice(-konamiCode.length);
        
        if (konami.join(',') === konamiCode.join(',')) {
            activatePrimalMode();
        }
    });
    
    // Secret click pattern on skull
    const skull = document.querySelector('.error-skull');
    let skullClicks = 0;
    
    if (skull) {
        skull.addEventListener('click', function() {
            skullClicks++;
            this.style.transform = 'translate(-50%, -50%) scale(1.2)';
            setTimeout(() => {
                this.style.transform = 'translate(-50%, -50%) scale(1)';
            }, 200);
            
            if (skullClicks >= 5) {
                showSecretMessage();
                skullClicks = 0;
            }
        });
    }
}

function activatePrimalMode() {
    document.body.style.filter = 'hue-rotate(120deg) saturate(1.5)';
    document.body.style.transition = 'filter 1s ease';
    
    showNotification('🔥 PRIMAL MODE ACTIVATED! 🔥', 'success');
    
    setTimeout(() => {
        document.body.style.filter = '';
    }, 5000);
}

function showSecretMessage() {
    const messages = [
        "The void whispers secrets to those who listen...",
        "You've found a hidden path in the darkness.",
        "The primal spirits acknowledge your persistence.",
        "Some treasures are worth the search...",
        "The black market has many layers..."
    ];
    
    const randomMessage = messages[Math.floor(Math.random() * messages.length)];
    showNotification(randomMessage, 'mystery');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `error-notification ${type}`;
    
    const iconMap = {
        success: 'check-circle',
        error: 'exclamation-circle',
        info: 'info-circle',
        mystery: 'eye'
    };
    
    notification.innerHTML = `
        <i class="fas fa-${iconMap[type] || 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Style the notification
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '1rem 1.5rem',
        background: type === 'success' ? 'rgba(40, 167, 69, 0.9)' : 
                   type === 'error' ? 'rgba(220, 53, 69, 0.9)' : 
                   type === 'mystery' ? 'rgba(138, 43, 226, 0.9)' :
                   'rgba(255, 140, 0, 0.9)',
        color: 'white',
        borderRadius: '10px',
        zIndex: '10000',
        display: 'flex',
        alignItems: 'center',
        gap: '0.5rem',
        fontFamily: 'Cinzel, serif',
        fontWeight: '600',
        boxShadow: '0 4px 15px rgba(0, 0, 0, 0.3)',
        transform: 'translateX(100%)',
        transition: 'transform 0.3s ease',
        maxWidth: '300px'
    });
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 4 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Utility function to create particle effects
function createParticleEffect(element) {
    const rect = element.getBoundingClientRect();
    const particles = 12;
    
    for (let i = 0; i < particles; i++) {
        const particle = document.createElement('div');
        particle.style.cssText = `
            position: fixed;
            width: 4px;
            height: 4px;
            background: #ff8c00;
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            left: ${rect.left + rect.width / 2}px;
            top: ${rect.top + rect.height / 2}px;
        `;
        
        document.body.appendChild(particle);
        
        const angle = (i / particles) * Math.PI * 2;
        const velocity = 100 + Math.random() * 50;
        const lifetime = 1000 + Math.random() * 500;
        
        particle.animate([
            { 
                transform: 'translate(0, 0) scale(1)',
                opacity: 1
            },
            { 
                transform: `translate(${Math.cos(angle) * velocity}px, ${Math.sin(angle) * velocity}px) scale(0)`,
                opacity: 0
            }
        ], {
            duration: lifetime,
            easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
        }).onfinish = () => {
            document.body.removeChild(particle);
        };
    }
}
