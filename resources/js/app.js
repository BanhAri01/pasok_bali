import Alpine from 'alpinejs';

Alpine.data('notaPasar', (baris, kuota) => ({
    terbaca: baris.map(() => false),
    antrean: [],

    get semuaTerbaca() {
        return this.terbaca.every(Boolean);
    },

    baca(i) {
        this.terbaca[i] = !this.terbaca[i];
    },

    bacaSemua() {
        this.antrean.forEach(clearTimeout);
        const ulang = this.semuaTerbaca;
        this.terbaca = baris.map(() => false);

        if (ulang) {
            return;
        }

        const tunda = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 160;
        this.antrean = baris.map((_, i) => setTimeout(() => (this.terbaca[i] = true), i * tunda));
    },

    hitung(kelompok) {
        return baris.reduce(
            (acc, item, i) => {
                if (item.kelompok === kelompok && this.terbaca[i]) {
                    acc.total += item.kg;
                    acc.lokal += item.lokal ? item.kg : 0;
                }

                return acc;
            },
            { total: 0, lokal: 0 },
        );
    },

    persen(kelompok) {
        const { total, lokal } = this.hitung(kelompok);

        return total > 0 ? Math.round((lokal / total) * 100) : null;
    },

    keterangan(kelompok) {
        const { total, lokal } = this.hitung(kelompok);

        if (total === 0) {
            return 'Ketuk barang di nota untuk menghitung.';
        }

        const kurang = (total * kuota[kelompok]) / 100 - lokal;

        return kurang <= 0.001
            ? `Memenuhi kuota dari ${this.kg(total)} kg`
            : `Kurang ${this.kg(kurang)} kg produk lokal`;
    },

    kg(nilai) {
        return nilai.toLocaleString('id-ID', { maximumFractionDigits: 1 });
    },
}));

window.Alpine = Alpine;
Alpine.start();
