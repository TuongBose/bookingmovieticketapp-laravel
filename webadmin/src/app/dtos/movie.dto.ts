export class MovieDTO {
  id: number;
  name: string;
  posterurl: string;
  releasedate: string;
  voteaverage: number;

  constructor(data: any) {
    this.id = data.id || 0;
    this.name = data.name || 'Unknown Title';
    this.posterurl = data.posterurl || 'no_image';
    this.releasedate = data.releasedate || 'Unknown Release Date';
    this.voteaverage = data.voteaverage || 0;
  }
}