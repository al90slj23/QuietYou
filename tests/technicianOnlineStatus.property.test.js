/**
 * Property 21: Technician Online Status Visibility
 * 技师在线状态可见性属性测试
 * Validates: Requirements 9.3, 9.4
 * 
 * 核心属性：
 * 1. 只有在线状态的技师才显示在搜索结果中
 * 2. 离线技师不会出现在搜索结果中
 * 3. 忙碌状态的技师不会出现在搜索结果中
 * 4. 状态切换的一致性
 */

import { describe, it, expect } from 'vitest';
import * as fc from 'fast-check';
import {
  TechnicianStatus,
  shouldShowInSearchResults,
  canReceiveNewOrders,
  toggleOnlineStatus,
  onOrderAccepted,
  onOrderCompleted,
  filterVisibleTechnicians,
  getStatusDisplayText
} from './lib/technicianOnlineStatus.js';

// 生成技师状态
const statusArb = fc.constantFrom(
  TechnicianStatus.ONLINE,
  TechnicianStatus.OFFLINE,
  TechnicianStatus.BUSY
);

// 生成技师对象
const technicianArb = fc.record({
  id: fc.uuid(),
  name: fc.string({ minLength: 1, maxLength: 20 }),
  status: statusArb,
  isActive: fc.boolean(),
  isCertified: fc.boolean()
});

// 生成技师列表
const technicianListArb = fc.array(technicianArb, { minLength: 0, maxLength: 20 });

describe('Property 21: Technician Online Status Visibility', () => {
  
  describe('搜索结果可见性', () => {
    
    it('在线且激活认证的技师应该显示在搜索结果中', () => {
      fc.assert(
        fc.property(technicianArb, (technician) => {
          const onlineTech = {
            ...technician,
            status: TechnicianStatus.ONLINE,
            isActive: true,
            isCertified: true
          };
          
          expect(shouldShowInSearchResults(onlineTech)).toBe(true);
        })
      );
    });

    it('离线技师不应该显示在搜索结果中', () => {
      fc.assert(
        fc.property(technicianArb, (technician) => {
          const offlineTech = {
            ...technician,
            status: TechnicianStatus.OFFLINE,
            isActive: true,
            isCertified: true
          };
          
          expect(shouldShowInSearchResults(offlineTech)).toBe(false);
        })
      );
    });

    it('忙碌状态的技师不应该显示在搜索结果中', () => {
      fc.assert(
        fc.property(technicianArb, (technician) => {
          const busyTech = {
            ...technician,
            status: TechnicianStatus.BUSY,
            isActive: true,
            isCertified: true
          };
          
          expect(shouldShowInSearchResults(busyTech)).toBe(false);
        })
      );
    });

    it('未激活账号的技师不应该显示在搜索结果中', () => {
      fc.assert(
        fc.property(technicianArb, statusArb, (technician, status) => {
          const inactiveTech = {
            ...technician,
            status: status,
            isActive: false,
            isCertified: true
          };
          
          expect(shouldShowInSearchResults(inactiveTech)).toBe(false);
        })
      );
    });

    it('未认证的技师不应该显示在搜索结果中', () => {
      fc.assert(
        fc.property(technicianArb, statusArb, (technician, status) => {
          const uncertifiedTech = {
            ...technician,
            status: status,
            isActive: true,
            isCertified: false
          };
          
          expect(shouldShowInSearchResults(uncertifiedTech)).toBe(false);
        })
      );
    });
  });

  describe('接单能力', () => {
    
    it('只有在线状态的技师可以接收新订单', () => {
      fc.assert(
        fc.property(technicianArb, (technician) => {
          const onlineTech = {
            ...technician,
            status: TechnicianStatus.ONLINE,
            isActive: true,
            isCertified: true
          };
          
          expect(canReceiveNewOrders(onlineTech)).toBe(true);
        })
      );
    });

    it('离线或忙碌状态的技师不能接收新订单', () => {
      fc.assert(
        fc.property(
          technicianArb,
          fc.constantFrom(TechnicianStatus.OFFLINE, TechnicianStatus.BUSY),
          (technician, status) => {
            const tech = {
              ...technician,
              status: status,
              isActive: true,
              isCertified: true
            };
            
            expect(canReceiveNewOrders(tech)).toBe(false);
          }
        )
      );
    });
  });

  describe('状态切换', () => {
    
    it('离线技师可以成功上线', () => {
      fc.assert(
        fc.property(technicianArb, (technician) => {
          const offlineTech = {
            ...technician,
            status: TechnicianStatus.OFFLINE,
            isActive: true,
            isCertified: true
          };
          
          const result = toggleOnlineStatus(offlineTech, true);
          
          expect(result.status).toBe(TechnicianStatus.ONLINE);
          expect(result.error).toBeNull();
        })
      );
    });

    it('在线技师可以成功下线', () => {
      fc.assert(
        fc.property(technicianArb, (technician) => {
          const onlineTech = {
            ...technician,
            status: TechnicianStatus.ONLINE,
            isActive: true,
            isCertified: true
          };
          
          const result = toggleOnlineStatus(onlineTech, false);
          
          expect(result.status).toBe(TechnicianStatus.OFFLINE);
          expect(result.error).toBeNull();
        })
      );
    });

    it('忙碌状态的技师不能下线', () => {
      fc.assert(
        fc.property(technicianArb, (technician) => {
          const busyTech = {
            ...technician,
            status: TechnicianStatus.BUSY,
            isActive: true,
            isCertified: true
          };
          
          const result = toggleOnlineStatus(busyTech, false);
          
          // 状态应该保持不变
          expect(result.status).toBe(TechnicianStatus.BUSY);
          expect(result.error).toBeTruthy();
        })
      );
    });

    it('未激活或未认证的技师不能上线', () => {
      fc.assert(
        fc.property(
          technicianArb,
          fc.boolean(),
          fc.boolean(),
          (technician, isActive, isCertified) => {
            // 至少有一个为false
            fc.pre(!isActive || !isCertified);
            
            const tech = {
              ...technician,
              status: TechnicianStatus.OFFLINE,
              isActive,
              isCertified
            };
            
            const result = toggleOnlineStatus(tech, true);
            
            expect(result.status).toBe(TechnicianStatus.OFFLINE);
            expect(result.error).toBeTruthy();
          }
        )
      );
    });
  });

  describe('订单状态联动', () => {
    
    it('接单后技师状态变为忙碌', () => {
      fc.assert(
        fc.property(technicianArb, (technician) => {
          const onlineTech = {
            ...technician,
            status: TechnicianStatus.ONLINE,
            isActive: true,
            isCertified: true
          };
          
          const result = onOrderAccepted(onlineTech);
          
          expect(result.status).toBe(TechnicianStatus.BUSY);
        })
      );
    });

    it('完成订单且无其他订单时恢复在线', () => {
      fc.assert(
        fc.property(technicianArb, (technician) => {
          const busyTech = {
            ...technician,
            status: TechnicianStatus.BUSY,
            isActive: true,
            isCertified: true
          };
          
          const result = onOrderCompleted(busyTech, false);
          
          expect(result.status).toBe(TechnicianStatus.ONLINE);
        })
      );
    });

    it('完成订单但还有其他订单时保持忙碌', () => {
      fc.assert(
        fc.property(technicianArb, (technician) => {
          const busyTech = {
            ...technician,
            status: TechnicianStatus.BUSY,
            isActive: true,
            isCertified: true
          };
          
          const result = onOrderCompleted(busyTech, true);
          
          expect(result.status).toBe(TechnicianStatus.BUSY);
        })
      );
    });
  });

  describe('列表过滤', () => {
    
    it('过滤后的列表只包含在线且激活认证的技师', () => {
      fc.assert(
        fc.property(technicianListArb, (technicians) => {
          const filtered = filterVisibleTechnicians(technicians);
          
          // 所有过滤后的技师都应该是在线、激活、认证的
          filtered.forEach(tech => {
            expect(tech.status).toBe(TechnicianStatus.ONLINE);
            expect(tech.isActive).toBe(true);
            expect(tech.isCertified).toBe(true);
          });
        })
      );
    });

    it('过滤后的列表数量不超过原列表', () => {
      fc.assert(
        fc.property(technicianListArb, (technicians) => {
          const filtered = filterVisibleTechnicians(technicians);
          
          expect(filtered.length).toBeLessThanOrEqual(technicians.length);
        })
      );
    });

    it('过滤结果与手动计数一致', () => {
      fc.assert(
        fc.property(technicianListArb, (technicians) => {
          const filtered = filterVisibleTechnicians(technicians);
          
          const expectedCount = technicians.filter(t => 
            t.status === TechnicianStatus.ONLINE && 
            t.isActive && 
            t.isCertified
          ).length;
          
          expect(filtered.length).toBe(expectedCount);
        })
      );
    });
  });

  describe('状态显示文本', () => {
    
    it('所有状态都有对应的显示文本', () => {
      fc.assert(
        fc.property(statusArb, (status) => {
          const text = getStatusDisplayText(status);
          
          expect(text).toBeTruthy();
          expect(typeof text).toBe('string');
          expect(text.length).toBeGreaterThan(0);
        })
      );
    });
  });
});
