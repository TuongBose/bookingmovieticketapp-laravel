import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListCinemaComponent } from './list-cinema.component';

describe('ListCinemaComponent', () => {
  let component: ListCinemaComponent;
  let fixture: ComponentFixture<ListCinemaComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ListCinemaComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ListCinemaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
