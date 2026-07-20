let transliterationEnabled = false;
let isNpLocaleContext = false;

window.initTransliteration = function(isNpEnabled) {
    isNpLocaleContext = isNpEnabled;
    transliterationEnabled = isNpEnabled;

    if (isNpLocaleContext) {
        
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key.toLowerCase() === 'g') {
                e.preventDefault();
                transliterationEnabled = !transliterationEnabled;
                showToast(transliterationEnabled ? 'Transliteration: Nepali (ON)' : 'Transliteration: English (OFF)');
            }
        });
    }
};

function showToast(message) {
    let toast = document.getElementById('transliteration-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'transliteration-toast';
        toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded shadow-lg z-50 text-sm font-medium transition-opacity duration-300';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.style.opacity = '1';
    
    clearTimeout(toast.hideTimeout);
    toast.hideTimeout = setTimeout(() => {
        toast.style.opacity = '0';
    }, 2000);
}

window.enableTransliteration = function(elementId) {
    if (!isNpLocaleContext) return;
    
    const el = document.getElementById(elementId);
    if (!el) return;
    

    el.addEventListener('keyup', handleTransliteration);
};

async function handleTransliteration(e) {
    if (!transliterationEnabled) return;
    if (e.key === ' ' || e.code === 'Space' || e.key === 'Enter') {
        await transliterateLastWord(e.target);
    }
}

async function transliterateLastWord(input) {
    const cursorPos = input.selectionStart;
    const text = input.value;
    
    const textBeforeCursor = text.substring(0, cursorPos);
    const words = textBeforeCursor.split(/([\s\n]+)/); 
    if (words.length === 0) return;
    
    let wordIndex = words.length - 1;
    while (wordIndex >= 0 && words[wordIndex].trim() === '') {
        wordIndex--;
    }
    
    if (wordIndex < 0) return;
    
    const wordToTransliterate = words[wordIndex];
    if (!/^[a-zA-Z]+$/.test(wordToTransliterate)) return;
    
    try {
        const response = await fetch(`https://inputtools.google.com/request?text=${encodeURIComponent(wordToTransliterate)}&itc=ne-t-i0-und&num=1&cp=0&cs=1&ie=utf-8&oe=utf-8&app=test`);
        const data = await response.json();
        
        if (data && data[0] === 'SUCCESS' && data[1] && data[1][0] && data[1][0][1] && data[1][0][1].length > 0) {
            const transliteratedWord = data[1][0][1][0];
            
            words[wordIndex] = transliteratedWord;
            const newTextBeforeCursor = words.join('');
            const afterCursor = text.substring(cursorPos);
            
            input.value = newTextBeforeCursor + afterCursor;
            
            const newCursorPos = newTextBeforeCursor.length;
            input.setSelectionRange(newCursorPos, newCursorPos);
            
           
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
    } catch (err) {
        console.error('Transliteration failed:', err);
    }
}
