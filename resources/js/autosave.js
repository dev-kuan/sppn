/**
 * Auto-save Utility for Assessment Form
 * Handles debounced saving with visual feedback
 */

class AutoSave {
    constructor(options = {}) {
        this.debounceTime = options.debounceTime || 300;
        this.batchDelay = options.batchDelay || 50;
        this.saveEndpoint = options.saveEndpoint;
        this.csrfToken = options.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
        this.onSuccess = options.onSuccess || (() => {});
        this.onError = options.onError || (() => {});
        this.onSaving = options.onSaving || (() => {});

        this.saveTimeout = null;
        this.batchTimeout = null;
        this.pendingChanges = new Map();
        this.isSaving = false;
        this.failedAttempts = 0;
        this.maxRetries = 3;

        console.log('AutoSave initialized with endpoint:', this.saveEndpoint);
    }

    /**
     * Queue a save request with intelligent batching
     */
    save(data) {
        console.log('AutoSave: Queueing save for', data);

        const key = `${data.observation_item_id}_${data.hari}`;
        this.pendingChanges.set(key, data);

        if (this.batchTimeout) {
            clearTimeout(this.batchTimeout);
        }

        this.batchTimeout = setTimeout(() => {
            this.debouncedSave();
        }, this.batchDelay);
    }

    /**
     * Debounced save to prevent too many requests
     */
    debouncedSave() {
        if (this.saveTimeout) {
            clearTimeout(this.saveTimeout);
        }

        this.saveTimeout = setTimeout(() => {
            this.processSaveQueue();
        }, this.debounceTime);
    }

    /**
     * Process the save queue
     */
    async processSaveQueue() {
        if (this.isSaving || this.pendingChanges.size === 0) {
            console.log('AutoSave: Skip processing -', {isSaving: this.isSaving, queueSize: this.pendingChanges.size});
            return;
        }

        console.log('AutoSave: Processing queue with', this.pendingChanges.size, 'items');

        this.isSaving = true;
        this.onSaving(true);

        const changesArray = Array.from(this.pendingChanges.values());
        const changesCopy = new Map(this.pendingChanges);
        this.pendingChanges.clear();

        try {
            if (changesArray.length === 1) {
                await this.saveSingle(changesArray[0]);
            } else {
                await this.saveInParallel(changesArray);
            }

            this.failedAttempts = 0;
            console.log('AutoSave: Successfully saved', changesArray.length, 'items');
        } catch (error) {
            console.error('AutoSave: Save error:', error);
            this.failedAttempts++;

            if (this.failedAttempts < this.maxRetries) {
                console.log('AutoSave: Retrying... Attempt', this.failedAttempts);
                changesCopy.forEach((value, key) => {
                    this.pendingChanges.set(key, value);
                });

                const retryDelay = Math.min(1000 * Math.pow(2, this.failedAttempts), 5000);
                setTimeout(() => {
                    this.processSaveQueue();
                }, retryDelay);
            } else {
                this.onError(error);
                this.failedAttempts = 0;
            }
        } finally {
            this.isSaving = false;
            this.onSaving(false);

            if (this.pendingChanges.size > 0) {
                setTimeout(() => this.processSaveQueue(), 100);
            }
        }
    }

    /**
     * Save a single observation
     */
    async saveSingle(data) {
        console.log('AutoSave: Sending request to', this.saveEndpoint, 'with data:', data);

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
        console.log('AutoSave: Server response:', result);

        if (response.ok && result.success) {
            this.onSuccess(result);
        } else {
            throw new Error(result.message || 'Save failed');
        }
    }

    /**
     * Save multiple observations in parallel
     */
    async saveInParallel(dataArray) {
        console.log('AutoSave: Saving in parallel:', dataArray.length, 'items');

        const batchSize = 3;
        const results = [];

        for (let i = 0; i < dataArray.length; i += batchSize) {
            const batch = dataArray.slice(i, i + batchSize);
            const promises = batch.map(data => this.saveSingle(data).catch(err => {
                console.error('AutoSave: Batch item failed:', err);
                throw err;
            }));

            const batchResults = await Promise.allSettled(promises);
            results.push(...batchResults);
        }

        const failed = results.filter(r => r.status === 'rejected');
        if (failed.length > 0) {
            throw new Error(`${failed.length} saves failed`);
        }

        const lastSuccess = results.reverse().find(r => r.status === 'fulfilled');
        if (lastSuccess?.value) {
            // Don't call onSuccess again as it was already called in saveSingle
        }
    }

    /**
     * Force save immediately (skip debounce)
     */
    async saveNow() {
        if (this.saveTimeout) {
            clearTimeout(this.saveTimeout);
        }
        if (this.batchTimeout) {
            clearTimeout(this.batchTimeout);
        }
        await this.processSaveQueue();
    }

    /**
     * Clear the save queue
     */
    clear() {
        if (this.saveTimeout) {
            clearTimeout(this.saveTimeout);
        }
        if (this.batchTimeout) {
            clearTimeout(this.batchTimeout);
        }
        this.pendingChanges.clear();
    }

    /**
     * Check if there are pending changes
     */
    hasPendingChanges() {
        return this.pendingChanges.size > 0 || this.isSaving;
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
                ` : type === 'error' ? `
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                ` : ''}
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(1rem)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Make available globally
if (typeof window !== 'undefined') {
    window.AutoSave = AutoSave;
    window.ScoreCalculator = ScoreCalculator;
    window.Toast = Toast;
}
