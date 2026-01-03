/**
 * Auto-save Utility for Assessment Form
 * Handles debounced saving with visual feedback
 */

class AutoSave {
    constructor(options = {}) {
        this.debounceTime = options.debounceTime || 1000;
        this.saveEndpoint = options.saveEndpoint;
        this.csrfToken = options.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
        this.onSuccess = options.onSuccess || (() => {});
        this.onError = options.onError || (() => {});
        this.onSaving = options.onSaving || (() => {});

        this.saveTimeout = null;
        this.requestQueue = [];
        this.isSaving = false;
    }

    /**
     * Queue a save request with debouncing
     */
    save(data) {
        // Clear previous timeout
        if (this.saveTimeout) {
            clearTimeout(this.saveTimeout);
        }

        // Add to queue
        this.requestQueue.push(data);

        // Debounce the save
        this.saveTimeout = setTimeout(() => {
            this.processSaveQueue();
        }, this.debounceTime);
    }

    /**
     * Process the save queue
     */
    async processSaveQueue() {
        if (this.isSaving || this.requestQueue.length === 0) {
            return;
        }

        this.isSaving = true;
        this.onSaving(true);

        // Get the latest request from queue
        const data = this.requestQueue[this.requestQueue.length - 1];
        this.requestQueue = [];

        try {
            const response = await fetch(this.saveEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                this.onSuccess(result);
            } else {
                throw new Error(result.message || 'Save failed');
            }
        } catch (error) {
            console.error('Auto-save error:', error);
            this.onError(error);
        } finally {
            this.isSaving = false;
            this.onSaving(false);

            // Process remaining queue if any
            if (this.requestQueue.length > 0) {
                setTimeout(() => this.processSaveQueue(), 100);
            }
        }
    }

    /**
     * Force save immediately (skip debounce)
     */
    async saveNow(data) {
        this.requestQueue = [data];
        await this.processSaveQueue();
    }

    /**
     * Clear the save queue
     */
    clear() {
        if (this.saveTimeout) {
            clearTimeout(this.saveTimeout);
        }
        this.requestQueue = [];
    }
}

/**
 * Score Calculator
 * Real-time score calculation for assessment
 */
class ScoreCalculator {
    constructor(observations, observationItems, daysInMonth) {
        this.observations = observations;
        this.observationItems = observationItems;
        this.daysInMonth = daysInMonth;
    }

    /**
     * Calculate scores for all variabels
     */
    calculateAll() {
        const variabels = this.groupByVariabel();
        const scores = {
            kepribadian: 0,
            kemandirian: 0,
            sikap: 0,
            mental: 0,
            total: 0
        };

        Object.keys(variabels).forEach(variabelId => {
            const aspects = variabels[variabelId];
            let variabelScore = 0;

            Object.keys(aspects).forEach(aspekId => {
                const items = aspects[aspekId];

                items.forEach(item => {
                    const checkedCount = this.getCheckedCount(item.id);
                    const frequency = this.calculateFrequency(item);

                    if (frequency > 0) {
                        const itemScore = (checkedCount / frequency) * item.bobot;
                        variabelScore += itemScore;
                    }
                });
            });

            // Map to score keys
            const variabelName = this.getVariabelName(variabelId);
            if (scores.hasOwnProperty(variabelName)) {
                scores[variabelName] = variabelScore;
            }
        });

        scores.total = scores.kepribadian + scores.kemandirian + scores.sikap + scores.mental;

        return scores;
    }

    /**
     * Group observation items by variabel and aspek
     */
    groupByVariabel() {
        const grouped = {};

        this.observationItems.forEach(item => {
            if (!grouped[item.variabel_id]) {
                grouped[item.variabel_id] = {};
            }
            if (!grouped[item.variabel_id][item.aspek_id]) {
                grouped[item.variabel_id][item.aspek_id] = [];
            }
            grouped[item.variabel_id][item.aspek_id].push(item);
        });

        return grouped;
    }

    /**
     * Get count of checked observations for an item
     */
    getCheckedCount(itemId) {
        return this.observations.filter(obs =>
            obs.observation_item_id === itemId && obs.is_checked
        ).length;
    }

    /**
     * Calculate frequency for an item
     */
    calculateFrequency(item) {
        if (item.use_dynamic_frequency && item.frequency_rule) {
            // Apply frequency rule
            const rule = item.frequency_rule.formula;
            for (let i = 0; i < rule.length; i++) {
                if (this.daysInMonth <= rule[i].max_days) {
                    return rule[i].frequency;
                }
            }
        }
        return item.frekuensi_bulan;
    }

    /**
     * Get variabel name from ID
     */
    getVariabelName(variabelId) {
        const map = {
            1: 'kepribadian',
            2: 'kemandirian',
            3: 'sikap',
            4: 'mental'
        };
        return map[variabelId] || 'unknown';
    }

    /**
     * Get category from score
     */
    getCategory(score, variabelType = 'normal') {
        if (variabelType === 'sikap' || variabelType === 'mental') {
            if (score >= 81) return 'Sangat Patuh / Sangat Sehat Mental';
            if (score >= 61) return 'Patuh / Sehat Mental';
            if (score >= 41) return 'Cukup Patuh / Cukup Sehat Mental';
            if (score >= 21) return 'Tidak Patuh / Tidak Sehat Mental';
            return 'Sangat Tidak Patuh / Sangat Tidak Sehat Mental';
        }

        if (score >= 81) return 'Sangat Baik';
        if (score >= 61) return 'Baik';
        if (score >= 41) return 'Cukup Baik';
        if (score >= 21) return 'Tidak Baik';
        return 'Sangat Tidak Baik';
    }
}

/**
 * Toast Notification
 */
class Toast {
    static show(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transition-all transform translate-y-0 opacity-100 ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' :
            type === 'info' ? 'bg-blue-500' : 'bg-gray-500'
        }`;

        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                ${type === 'success' ? `
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                ` : ''}
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(1rem)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { AutoSave, ScoreCalculator, Toast };
}
