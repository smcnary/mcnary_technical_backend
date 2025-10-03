export declare class CSVFormatter {
    private inputDir;
    private outputDir;
    constructor(inputDir?: string, outputDir?: string);
    formatCSVFile(inputFile: string): void;
    formatAllCSVs(): void;
    private parseCSVRow;
    private formatRow;
    private writeFormattedOutput;
    private writeHTMLOutput;
    createSummaryReport(): void;
}
//# sourceMappingURL=csv-formatter.d.ts.map