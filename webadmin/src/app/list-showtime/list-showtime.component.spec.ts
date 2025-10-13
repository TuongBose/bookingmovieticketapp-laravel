import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListShowtimeComponent } from './list-showtime.component';

describe('ListShowtimeComponent', () => {
  let component: ListShowtimeComponent;
  let fixture: ComponentFixture<ListShowtimeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ListShowtimeComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ListShowtimeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
