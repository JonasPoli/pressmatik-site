<?php

namespace App\Service;

class ProductCatalogService
{
    private array $catalog = [];

    public function __construct()
    {
        $this->initializeCatalog();
    }

    private function initializeCatalog(): void
    {
        $this->catalog = [
            // 1. PRENSAS HIDRÁULICAS
            'prensas-hidraulicas-tipo-c' => [
                'slug' => 'prensas-hidraulicas-tipo-c',
                'category' => 'hydraulic',
                'name_key' => 'catalog.hydraulic.tipo_c.name',
                'desc_key' => 'catalog.hydraulic.tipo_c.desc',
                'tonnage' => '25 ~ 160 Ton',
                'img' => '/images/prensa-hidraulica-tipo-c-linha-st.png',
                'has_specs' => true,
                'subproducts' => [
                    ['model' => 'PMC-ST', 'name_key' => 'catalog.hydraulic.tipo_c.sub.pmc_st.name', 'desc_key' => 'catalog.hydraulic.tipo_c.sub.pmc_st.desc', 'tag' => 'Standard', 'img' => '/images/prensa-hidraulica-tipo-c-linha-st.png'],
                    ['model' => 'PMC-BC', 'name_key' => 'catalog.hydraulic.tipo_c.sub.pmc_bc.name', 'desc_key' => 'catalog.hydraulic.tipo_c.sub.pmc_bc.desc', 'tag' => 'Compact', 'img' => '/images/pmc-bc.png'],
                    ['model' => 'PMC-GT', 'name_key' => 'catalog.hydraulic.tipo_c.sub.pmc_gt.name', 'desc_key' => 'catalog.hydraulic.tipo_c.sub.pmc_gt.desc', 'tag' => 'High Output', 'img' => '/images/pmc-gt.png'],
                    ['model' => 'PMC-TR', 'name_key' => 'catalog.hydraulic.tipo_c.sub.pmc_tr.name', 'desc_key' => 'catalog.hydraulic.tipo_c.sub.pmc_tr.desc', 'tag' => 'Deflashing', 'img' => '/images/modelos/pmc-tr.png'],
                    ['model' => 'PMC-MT', 'name_key' => 'catalog.hydraulic.tipo_c.sub.pmc_mt.name', 'desc_key' => 'catalog.hydraulic.tipo_c.sub.pmc_mt.desc', 'tag' => 'Heavy Die', 'img' => '/images/modelos/pmc-mt.png'],
                    ['model' => 'PMC-AL', 'name_key' => 'catalog.hydraulic.tipo_c.sub.pmc_al.name', 'desc_key' => 'catalog.hydraulic.tipo_c.sub.pmc_al.desc', 'tag' => 'Precision', 'img' => '/images/modelos/pmc-al.png'],
                    ['model' => 'PMC-HZ', 'name_key' => 'catalog.hydraulic.tipo_c.sub.pmc_hz.name', 'desc_key' => 'catalog.hydraulic.tipo_c.sub.pmc_hz.desc', 'tag' => 'Assembly', 'img' => '/images/modelos/pmc-hz.png'],
                    ['model' => 'PMC-ES', 'name_key' => 'catalog.hydraulic.tipo_c.sub.pmc_es.name', 'desc_key' => 'catalog.hydraulic.tipo_c.sub.pmc_es.desc', 'tag' => 'Custom', 'img' => '/images/pmc-es.png'],
                ],
                'applications' => [
                    ['name_key' => 'catalog.applications.corte', 'img' => '/images/aplicacoes/corte.png'],
                    ['name_key' => 'catalog.applications.dobra', 'img' => '/images/aplicacoes/dobra.png'],
                    ['name_key' => 'catalog.applications.duplo_efeito', 'img' => '/images/aplicacoes/duplo-efeito.png'],
                    ['name_key' => 'catalog.applications.estampagem', 'img' => '/images/aplicacoes/estampagem.png'],
                    ['name_key' => 'catalog.applications.montagem', 'img' => '/images/aplicacoes/montagem.png'],
                    ['name_key' => 'catalog.applications.progressivo', 'img' => '/images/aplicacoes/progressivo.png'],
                    ['name_key' => 'catalog.applications.rebarbacao', 'img' => '/images/aplicacoes/rebarbacao.png'],
                    ['name_key' => 'catalog.applications.repuxo', 'img' => '/images/aplicacoes/repuxo.png'],
                ],
                'features' => [
                    'product_detail.features_list.0',
                    'product_detail.features_list.1',
                    'product_detail.features_list.2',
                    'product_detail.features_list.3',
                    'product_detail.features_list.4',
                    'product_detail.features_list.5',
                    'product_detail.features_list.6',
                ],
                'std_configs' => [
                    'product_detail.std.0',
                    'product_detail.std.1',
                    'product_detail.std.2',
                    'product_detail.std.3',
                    'product_detail.std.4',
                    'product_detail.std.16',
                    'product_detail.std.18',
                    'product_detail.std.20',
                ],
                'opt_configs' => [
                    'product_detail.opt.0',
                    'product_detail.opt.1',
                    'product_detail.opt.3',
                    'product_detail.opt.5',
                ]
            ],
            'prensas-hidraulicas-tipo-c-duplo' => [
                'slug' => 'prensas-hidraulicas-tipo-c-duplo',
                'category' => 'hydraulic',
                'name_key' => 'catalog.hydraulic.tipo_c_duplo.name',
                'desc_key' => 'catalog.hydraulic.tipo_c_duplo.desc',
                'tonnage' => '50 ~ 315 Ton',
                'img' => '/images/prensa-hidraulica-tipo-c-duplo-linha-st.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'PMCD-ST', 'name_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_st.name', 'desc_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_st.desc', 'tag' => 'Standard', 'img' => '/images/pmc-st.png'],
                    ['model' => 'PMCD-GT', 'name_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_gt.name', 'desc_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_gt.desc', 'tag' => 'High Output', 'img' => '/images/pmc-gt.png'],
                    ['model' => 'PMCD-TR', 'name_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_tr.name', 'desc_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_tr.desc', 'tag' => 'Deflashing', 'img' => '/images/pmc-hz.png'],
                    ['model' => 'PMCD-MT', 'name_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_mt.name', 'desc_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_mt.desc', 'tag' => 'Heavy Die', 'img' => '/images/pmc-mt.png'],
                    ['model' => 'PMCD-BC', 'name_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_bc.name', 'desc_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_bc.desc', 'tag' => 'Compact', 'img' => '/images/pmc-bc.png'],
                    ['model' => 'PMCD-ES', 'name_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_es.name', 'desc_key' => 'catalog.hydraulic.tipo_c_duplo.sub.pmcd_es.desc', 'tag' => 'Custom', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [
                    'product_detail.features_list.0',
                    'product_detail.features_list.2',
                    'product_detail.features_list.6',
                ],
                'std_configs' => [
                    'product_detail.std.0',
                    'product_detail.std.2',
                    'product_detail.std.16',
                ],
                'opt_configs' => [
                    'product_detail.opt.0',
                    'product_detail.opt.1',
                ]
            ],
            'prensas-hidraulicas-4-colunas' => [
                'slug' => 'prensas-hidraulicas-4-colunas',
                'category' => 'hydraulic',
                'name_key' => 'catalog.hydraulic.colunas.name',
                'desc_key' => 'catalog.hydraulic.colunas.desc',
                'tonnage' => '50 ~ 2000 Ton',
                'img' => '/images/4-colunas.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'PM4C-ST', 'name_key' => 'catalog.hydraulic.colunas.sub.pm4c_st.name', 'desc_key' => 'catalog.hydraulic.colunas.sub.pm4c_st.desc', 'tag' => 'Double Effect', 'img' => '/images/pmc-st.png'],
                    ['model' => 'PM4C-RP', 'name_key' => 'catalog.hydraulic.colunas.sub.pm4c_rp.name', 'desc_key' => 'catalog.hydraulic.colunas.sub.pm4c_rp.desc', 'tag' => 'Triple Effect', 'img' => '/images/pmc-mt.png'],
                    ['model' => 'PM4C-TR', 'name_key' => 'catalog.hydraulic.colunas.sub.pm4c_tr.name', 'desc_key' => 'catalog.hydraulic.colunas.sub.pm4c_tr.desc', 'tag' => 'Deflashing', 'img' => '/images/pmc-hz.png'],
                    ['model' => 'PM4C-TY', 'name_key' => 'catalog.hydraulic.colunas.sub.pm4c_ty.name', 'desc_key' => 'catalog.hydraulic.colunas.sub.pm4c_ty.desc', 'tag' => 'Tryout', 'img' => '/images/pmc-es.png'],
                    ['model' => 'PM4C-PD', 'name_key' => 'catalog.hydraulic.colunas.sub.pm4c_pd.name', 'desc_key' => 'catalog.hydraulic.colunas.sub.pm4c_pd.desc', 'tag' => 'Tableting', 'img' => '/images/pmc-bc.png'],
                    ['model' => 'PMH4-CT', 'name_key' => 'catalog.hydraulic.colunas.sub.pmh4_ct.name', 'desc_key' => 'catalog.hydraulic.colunas.sub.pmh4_ct.desc', 'tag' => 'Cutting', 'img' => '/images/pmc-gt.png'],
                    ['model' => 'PM4C-ES', 'name_key' => 'catalog.hydraulic.colunas.sub.pm4c_es.name', 'desc_key' => 'catalog.hydraulic.colunas.sub.pm4c_es.desc', 'tag' => 'Custom', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [
                    'product_detail.features_list.0',
                    'product_detail.features_list.6',
                ],
                'std_configs' => [
                    'product_detail.std.0',
                    'product_detail.std.16',
                ],
                'opt_configs' => [
                    'product_detail.opt.0',
                    'product_detail.opt.1',
                ]
            ],
            'prensas-hidraulicas-tipo-h' => [
                'slug' => 'prensas-hidraulicas-tipo-h',
                'category' => 'hydraulic',
                'name_key' => 'catalog.hydraulic.tipo_h.name',
                'desc_key' => 'catalog.hydraulic.tipo_h.desc',
                'tonnage' => '100 ~ 3000 Ton',
                'img' => '/images/tipo-h.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'PMH-ST', 'name_key' => 'catalog.hydraulic.tipo_h.sub.pmh_st.name', 'desc_key' => 'catalog.hydraulic.tipo_h.sub.pmh_st.desc', 'tag' => 'Hammer', 'img' => '/images/pmc-st.png'],
                    ['model' => 'PMH-PR', 'name_key' => 'catalog.hydraulic.tipo_h.sub.pmh_pr.name', 'desc_key' => 'catalog.hydraulic.tipo_h.sub.pmh_pr.desc', 'tag' => '4 & 8 Points', 'img' => '/images/pmc-mt.png'],
                    ['model' => 'PMH-WK', 'name_key' => 'catalog.hydraulic.tipo_h.sub.pmh_wk.name', 'desc_key' => 'catalog.hydraulic.tipo_h.sub.pmh_wk.desc', 'tag' => 'Workshop', 'img' => '/images/pmc-bc.png'],
                    ['model' => 'PMH-WP', 'name_key' => 'catalog.hydraulic.tipo_h.sub.pmh_wp.name', 'desc_key' => 'catalog.hydraulic.tipo_h.sub.pmh_wp.desc', 'tag' => 'Gantry', 'img' => '/images/pmc-hz.png'],
                    ['model' => 'PMH-VB', 'name_key' => 'catalog.hydraulic.tipo_h.sub.pmh_vb.name', 'desc_key' => 'catalog.hydraulic.tipo_h.sub.pmh_vb.desc', 'tag' => 'Vulcanizing', 'img' => '/images/pmc-es.png'],
                    ['model' => 'PMH-WT', 'name_key' => 'catalog.hydraulic.tipo_h.sub.pmh_wt.name', 'desc_key' => 'catalog.hydraulic.tipo_h.sub.pmh_wt.desc', 'tag' => 'Tire Assembly', 'img' => '/images/pmc-gt.png'],
                    ['model' => 'PMH-ES', 'name_key' => 'catalog.hydraulic.tipo_h.sub.pmh_es.name', 'desc_key' => 'catalog.hydraulic.tipo_h.sub.pmh_es.desc', 'tag' => 'Custom', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [
                    'product_detail.features_list.0',
                    'product_detail.features_list.6',
                ],
                'std_configs' => [
                    'product_detail.std.0',
                    'product_detail.std.16',
                ],
                'opt_configs' => [
                    'product_detail.opt.0',
                    'product_detail.opt.1',
                ]
            ],
            'prensas-hidraulicas-especiais' => [
                'slug' => 'prensas-hidraulicas-especiais',
                'category' => 'hydraulic',
                'name_key' => 'catalog.hydraulic.especiais.name',
                'desc_key' => 'catalog.hydraulic.especiais.desc',
                'tonnage' => 'Custom',
                'img' => '/images/especiais.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Sob Consulta', 'name_key' => 'catalog.common.special_project.name', 'desc_key' => 'catalog.common.special_project.desc', 'tag' => 'Turnkey', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [
                    'product_detail.features_list.0',
                    'product_detail.features_list.7',
                ],
                'std_configs' => [],
                'opt_configs' => [
                    'product_detail.opt.0',
                ]
            ],

            // 2. PRENSAS SERVO-HIDRÁULICAS
            'prensas-servo-hidraulicas-servo-bombas' => [
                'slug' => 'prensas-servo-hidraulicas-servo-bombas',
                'category' => 'servo_hydraulic',
                'name_key' => 'catalog.servo_hydraulic.bombas.name',
                'desc_key' => 'catalog.servo_hydraulic.bombas.desc',
                'tonnage' => '50 ~ 2000 Ton',
                'img' => '/images/pmc-es.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Sob Consulta', 'name_key' => 'catalog.servo_hydraulic.bombas.sub.servobombas.name', 'desc_key' => 'catalog.servo_hydraulic.bombas.sub.servobombas.desc', 'tag' => 'Eco', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [
                    'product_detail.features_list.0',
                    'product_detail.features_list.6',
                ],
                'std_configs' => [
                    'product_detail.std.0',
                    'product_detail.std.16',
                ],
                'opt_configs' => [
                    'product_detail.opt.0',
                ]
            ],

            // 3. PRENSAS MECÂNICAS
            'prensas-mecanicas-mecanicas' => [
                'slug' => 'prensas-mecanicas-mecanicas',
                'category' => 'mechanical',
                'name_key' => 'catalog.mechanical.mecanicas.name',
                'desc_key' => 'catalog.mechanical.mecanicas.desc',
                'tonnage' => '25 ~ 1000 Ton',
                'img' => '/images/pmc-st.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'ST-Series C-Frame', 'name_key' => 'catalog.mechanical.mecanicas.sub.st_c_frame.name', 'desc_key' => 'catalog.mechanical.mecanicas.sub.st_c_frame.desc', 'tag' => 'C-Frame', 'img' => '/images/pmc-st.png'],
                    ['model' => 'ST-Series Deep-Throat', 'name_key' => 'catalog.mechanical.mecanicas.sub.st_deep_throat.name', 'desc_key' => 'catalog.mechanical.mecanicas.sub.st_deep_throat.desc', 'tag' => 'Deep Throat', 'img' => '/images/pmc-st.png'],
                    ['model' => 'STC-Series', 'name_key' => 'catalog.mechanical.mecanicas.sub.stc.name', 'desc_key' => 'catalog.mechanical.mecanicas.sub.stc.desc', 'tag' => 'Double Crank', 'img' => '/images/pmc-gt.png'],
                    ['model' => 'STB-Series', 'name_key' => 'catalog.mechanical.mecanicas.sub.stb.name', 'desc_key' => 'catalog.mechanical.mecanicas.sub.stb.desc', 'tag' => 'D-Frame', 'img' => '/images/pmc-hz.png'],
                    ['model' => 'STV-Series', 'name_key' => 'catalog.mechanical.mecanicas.sub.stv.name', 'desc_key' => 'catalog.mechanical.mecanicas.sub.stv.desc', 'tag' => 'H-Frame', 'img' => '/images/pmc-mt.png'],
                    ['model' => 'STD-Series', 'name_key' => 'catalog.mechanical.mecanicas.sub.std.name', 'desc_key' => 'catalog.mechanical.mecanicas.sub.std.desc', 'tag' => 'Straight Side', 'img' => '/images/pmc-es.png'],
                    ['model' => 'STE-Series', 'name_key' => 'catalog.mechanical.mecanicas.sub.ste.name', 'desc_key' => 'catalog.mechanical.mecanicas.sub.ste.desc', 'tag' => 'Straight Side', 'img' => '/images/pmc-es.png'],
                    ['model' => 'STF-Series', 'name_key' => 'catalog.mechanical.mecanicas.sub.stf.name', 'desc_key' => 'catalog.mechanical.mecanicas.sub.stf.desc', 'tag' => 'Straight Side', 'img' => '/images/pmc-es.png'],
                    ['model' => 'STN-Series', 'name_key' => 'catalog.mechanical.mecanicas.sub.stn.name', 'desc_key' => 'catalog.mechanical.mecanicas.sub.stn.desc', 'tag' => 'Eccentric', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [
                    'product_detail.features_list.0',
                    'product_detail.features_list.1',
                    'product_detail.features_list.2',
                ],
                'std_configs' => [],
                'opt_configs' => []
            ],
            'prensas-mecanicas-servo' => [
                'slug' => 'prensas-mecanicas-servo',
                'category' => 'mechanical',
                'name_key' => 'catalog.mechanical.servo.name',
                'desc_key' => 'catalog.mechanical.servo.desc',
                'tonnage' => '40 ~ 800 Ton',
                'img' => '/images/pmc-es.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'STA-Series', 'name_key' => 'catalog.mechanical.servo.sub.sta.name', 'desc_key' => 'catalog.mechanical.servo.sub.sta.desc', 'tag' => 'Servo C-Frame', 'img' => '/images/pmc-st.png'],
                    ['model' => 'STC-Series', 'name_key' => 'catalog.mechanical.servo.sub.stc.name', 'desc_key' => 'catalog.mechanical.servo.sub.stc.desc', 'tag' => 'Servo Double Crank', 'img' => '/images/pmc-gt.png'],
                    ['model' => 'STD-Series', 'name_key' => 'catalog.mechanical.servo.sub.std.name', 'desc_key' => 'catalog.mechanical.servo.sub.std.desc', 'tag' => 'Servo H-Frame', 'img' => '/images/pmc-mt.png'],
                    ['model' => 'STE-Series', 'name_key' => 'catalog.mechanical.servo.sub.ste.name', 'desc_key' => 'catalog.mechanical.servo.sub.ste.desc', 'tag' => 'Servo Double Crank H-Frame', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [
                    'product_detail.features_list.0',
                    'product_detail.features_list.6',
                ],
                'std_configs' => [],
                'opt_configs' => []
            ],
            'prensas-mecanicas-alta-velocidade' => [
                'slug' => 'prensas-mecanicas-alta-velocidade',
                'category' => 'mechanical',
                'name_key' => 'catalog.mechanical.alta_velocidade.name',
                'desc_key' => 'catalog.mechanical.alta_velocidade.desc',
                'tonnage' => '30 ~ 500 Ton',
                'img' => '/images/pmc-gt.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'STS-Series', 'name_key' => 'catalog.mechanical.alta.sub.sts.name', 'desc_key' => 'catalog.mechanical.alta.sub.sts.desc', 'tag' => 'High Speed', 'img' => '/images/pmc-bc.png'],
                    ['model' => 'MARX-Series', 'name_key' => 'catalog.mechanical.alta.sub.marx.name', 'desc_key' => 'catalog.mechanical.alta.sub.marx.desc', 'tag' => 'Knuckle Joint', 'img' => '/images/pmc-hz.png'],
                    ['model' => 'MDH-Series', 'name_key' => 'catalog.mechanical.alta.sub.mdh.name', 'desc_key' => 'catalog.mechanical.alta.sub.mdh.desc', 'tag' => 'H-Frame High Speed', 'img' => '/images/pmc-mt.png'],
                    ['model' => 'DDH-Series', 'name_key' => 'catalog.mechanical.alta.sub.ddh.name', 'desc_key' => 'catalog.mechanical.alta.sub.ddh.desc', 'tag' => 'Motor Lamination', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [],
                'std_configs' => [],
                'opt_configs' => []
            ],

            // 4. EQUIPAMENTOS
            'equipamentos-yokes' => [
                'slug' => 'equipamentos-yokes',
                'category' => 'equipments',
                'name_key' => 'catalog.equipments.yokes.name',
                'desc_key' => 'catalog.equipments.yokes.desc',
                'tonnage' => 'Custom',
                'img' => '/images/pmc-bc.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Yokes Rebitagem', 'name_key' => 'catalog.equipments.yokes.sub.rebitagem.name', 'desc_key' => 'catalog.equipments.yokes.sub.rebitagem.desc', 'tag' => 'Riveting', 'img' => '/images/pmc-bc.png'],
                    ['model' => 'Yokes Puncionamento', 'name_key' => 'catalog.equipments.yokes.sub.puncionamento.name', 'desc_key' => 'catalog.equipments.yokes.sub.puncionamento.desc', 'tag' => 'Punching', 'img' => '/images/pmc-hz.png'],
                    ['model' => 'Yokes Especiais', 'name_key' => 'catalog.equipments.yokes.sub.especiais.name', 'desc_key' => 'catalog.equipments.yokes.sub.especiais.desc', 'tag' => 'Custom', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [],
                'std_configs' => [],
                'opt_configs' => []
            ],
            'equipamentos-unidades-de-forca' => [
                'slug' => 'equipamentos-unidades-de-forca',
                'category' => 'equipments',
                'name_key' => 'catalog.equipments.unidades.name',
                'desc_key' => 'catalog.equipments.unidades.desc',
                'tonnage' => 'Custom',
                'img' => '/images/pmc-st.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Unidades Mini', 'name_key' => 'catalog.equipments.unidades.sub.mini.name', 'desc_key' => 'catalog.equipments.unidades.sub.mini.desc', 'tag' => 'Mini', 'img' => '/images/pmc-bc.png'],
                    ['model' => 'Unidades Stander', 'name_key' => 'catalog.equipments.unidades.sub.stander.name', 'desc_key' => 'catalog.equipments.unidades.sub.stander.desc', 'tag' => 'Standard', 'img' => '/images/pmc-st.png'],
                    ['model' => 'Unidades Especiais', 'name_key' => 'catalog.equipments.unidades.sub.especiais.name', 'desc_key' => 'catalog.equipments.unidades.sub.especiais.desc', 'tag' => 'Custom', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [],
                'std_configs' => [],
                'opt_configs' => []
            ],
            'equipamentos-cilindros' => [
                'slug' => 'equipamentos-cilindros',
                'category' => 'equipments',
                'name_key' => 'catalog.equipments.cilindros.name',
                'desc_key' => 'catalog.equipments.cilindros.desc',
                'tonnage' => 'Custom',
                'img' => '/images/pmc-hz.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Cilindro Normatizado', 'name_key' => 'catalog.equipments.cilindros.sub.normatizado.name', 'desc_key' => 'catalog.equipments.cilindros.sub.normatizado.desc', 'tag' => 'ISO/NFPA', 'img' => '/images/pmc-hz.png'],
                    ['model' => 'Cilindro Especial', 'name_key' => 'catalog.equipments.cilindros.sub.especial.name', 'desc_key' => 'catalog.equipments.cilindros.sub.especial.desc', 'tag' => 'Custom', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [],
                'std_configs' => [],
                'opt_configs' => []
            ],
            'equipamentos-transferencia-e-filtracao' => [
                'slug' => 'equipamentos-transferencia-e-filtracao',
                'category' => 'equipments',
                'name_key' => 'catalog.equipments.transferencia.name',
                'desc_key' => 'catalog.equipments.transferencia.desc',
                'tonnage' => 'Custom',
                'img' => '/images/pmc-gt.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Sistemas de Filtração', 'name_key' => 'catalog.equipments.transferencia.sub.filtracao.name', 'desc_key' => 'catalog.equipments.transferencia.sub.filtracao.desc', 'tag' => 'Filter', 'img' => '/images/pmc-gt.png'],
                ],
                'features' => [],
                'std_configs' => [],
                'opt_configs' => []
            ],
            'equipamentos-plataformas' => [
                'slug' => 'equipamentos-plataformas',
                'category' => 'equipments',
                'name_key' => 'catalog.equipments.plataformas.name',
                'desc_key' => 'catalog.equipments.plataformas.desc',
                'tonnage' => 'Custom',
                'img' => '/images/pmc-mt.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Plataformas Industriais', 'name_key' => 'catalog.equipments.plataformas.sub.plataformas.name', 'desc_key' => 'catalog.equipments.plataformas.sub.plataformas.desc', 'tag' => 'Platform', 'img' => '/images/pmc-mt.png'],
                ],
                'features' => [],
                'std_configs' => [],
                'opt_configs' => []
            ],
            'equipamentos-tombadores' => [
                'slug' => 'equipamentos-tombadores',
                'category' => 'equipments',
                'name_key' => 'catalog.equipments.tombadores.name',
                'desc_key' => 'catalog.equipments.tombadores.desc',
                'tonnage' => 'Custom',
                'img' => '/images/pmc-hz.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Tombadores de Moldes', 'name_key' => 'catalog.equipments.tombadores.sub.tombadores.name', 'desc_key' => 'catalog.equipments.tombadores.sub.tombadores.desc', 'tag' => 'Tilter', 'img' => '/images/pmc-hz.png'],
                ],
                'features' => [],
                'std_configs' => [],
                'opt_configs' => []
            ],
            'equipamentos-especiais' => [
                'slug' => 'equipamentos-especiais',
                'category' => 'equipments',
                'name_key' => 'catalog.equipments.especiais.name',
                'desc_key' => 'catalog.equipments.especiais.desc',
                'tonnage' => 'Custom',
                'img' => '/images/pmc-es.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Projetos Especiais', 'name_key' => 'catalog.common.special_project.name', 'desc_key' => 'catalog.common.special_project.desc', 'tag' => 'Turnkey', 'img' => '/images/pmc-es.png'],
                ],
                'features' => [],
                'std_configs' => [],
                'opt_configs' => []
            ],

            // 5. PEÇAS E ACESSÓRIOS
            'pecas-e-acessorios-hidraulica' => [
                'slug' => 'pecas-e-acessorios-hidraulica',
                'category' => 'parts',
                'name_key' => 'catalog.parts.hidraulica.name',
                'desc_key' => 'catalog.parts.hidraulica.desc',
                'tonnage' => null,
                'img' => '/images/pmc-st.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Bombas', 'name_key' => 'catalog.parts.hidraulica.sub.bombas.name', 'desc_key' => 'catalog.parts.hidraulica.sub.bombas.desc', 'tag' => 'Pumps', 'img' => '/images/pmc-st.png'],
                    ['model' => 'Válvulas', 'name_key' => 'catalog.parts.hidraulica.sub.valvulas.name', 'desc_key' => 'catalog.parts.hidraulica.sub.valvulas.desc', 'tag' => 'Valves', 'img' => '/images/pmc-gt.png'],
                    ['model' => 'Vedações', 'name_key' => 'catalog.parts.hidraulica.sub.vedacoes.name', 'desc_key' => 'catalog.parts.hidraulica.sub.vedacoes.desc', 'tag' => 'Seals', 'img' => '/images/pmc-bc.png'],
                    ['model' => 'Filtros', 'name_key' => 'catalog.parts.hidraulica.sub.filtros.name', 'desc_key' => 'catalog.parts.hidraulica.sub.filtros.desc', 'tag' => 'Filters', 'img' => '/images/pmc-hz.png'],
                ],
                'features' => [],
                'std_configs' => [],
                'opt_configs' => []
            ],
            'pecas-e-acessorios-eletroeletronica' => [
                'slug' => 'pecas-e-acessorios-eletroeletronica',
                'category' => 'parts',
                'name_key' => 'catalog.parts.eletro.name',
                'desc_key' => 'catalog.parts.eletro.desc',
                'tonnage' => null,
                'img' => '/images/pmc-es.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Automação', 'name_key' => 'catalog.parts.eletro.sub.automacao.name', 'desc_key' => 'catalog.parts.eletro.sub.automacao.desc', 'tag' => 'PLC', 'img' => '/images/pmc-es.png'],
                    ['model' => 'Potência e Comando', 'name_key' => 'catalog.parts.eletro.sub.potencia.name', 'desc_key' => 'catalog.parts.eletro.sub.potencia.desc', 'tag' => 'Panel', 'img' => '/images/pmc-mt.png'],
                ],
                'features' => [],
                'std_configs' => [],
                'opt_configs' => []
            ],
            'pecas-e-acessorios-acessorios' => [
                'slug' => 'pecas-e-acessorios-acessorios',
                'category' => 'parts',
                'name_key' => 'catalog.parts.acessorios.name',
                'desc_key' => 'catalog.parts.acessorios.desc',
                'tonnage' => null,
                'img' => '/images/pmc-hz.png',
                'has_specs' => false,
                'subproducts' => [
                    ['model' => 'Acessórios NR12', 'name_key' => 'catalog.parts.acessorios.sub.nr12.name', 'desc_key' => 'catalog.parts.acessorios.sub.nr12.desc', 'tag' => 'Safety', 'img' => '/images/pmc-hz.png'],
                    ['model' => 'Acessórios Diversos', 'name_key' => 'catalog.parts.acessorios.sub.diversos.name', 'desc_key' => 'catalog.parts.acessorios.sub.diversos.desc', 'tag' => 'Misc', 'img' => '/images/pmc-st.png'],
                ],
                'features' => [],
                'std_configs' => [],
                'opt_configs' => []
            ]
        ];

        // Specific PMC-ST Table specs for tipo-c
        $this->catalog['prensas-hidraulicas-tipo-c']['specs_table'] = [
            'models' => ['PMC-25', 'PMC-35', 'PMC-45', 'PMC-60', 'PMC-80', 'PMC-110', 'PMC-160'],
            'headers' => ['V-type', 'H-type'],
            'rows' => [
                ['label_key' => 'product_detail.spec_capacity', 'unit' => 'Ton', 'values' => [25, 35, 45, 60, 80, 110, 160], 'span' => 2],
                ['label_key' => 'product_detail.spec_rated_tonnage_point', 'unit' => 'mm', 'values' => [['3.2', '1.6'], ['3.2', '1.6'], ['3.2', '1.6'], ['4.0', '2.0'], ['4.0', '2.0'], ['6.0', '3.0'], ['6.0', '3.0']]],
                ['label_key' => 'product_detail.spec_speed', 'unit' => 'S.P.M', 'values' => [['60~140', '130~200'], ['40~120', '110~180'], ['40~100', '110~150'], ['35~90', '80~120'], ['35~80', '80~120'], ['30~60', '60~90'], ['20~50', '40~70']]],
                ['label_key' => 'product_detail.spec_stroke', 'unit' => 'mm', 'values' => [['60', '30'], ['70', '40'], ['80', '50'], ['120', '60'], ['150', '70'], ['180', '80'], ['200', '90']]],
                ['label_key' => 'product_detail.spec_die_height', 'unit' => 'mm', 'values' => [['200', '215'], ['220', '235'], ['250', '265'], ['310', '340'], ['340', '380'], ['360', '410'], ['460', '510']]],
                ['label_key' => 'product_detail.spec_slide_adjustment', 'unit' => 'mm', 'values' => [50, 55, 60, 75, 80, 80, 100], 'span' => 2],
                ['label_key' => 'product_detail.spec_slide_size', 'unit' => 'mm', 'values' => ['470*230*50', '520*250*50', '560*340*60', '700*400*70', '770*420*70', '910*470*80', '990*550*90'], 'span' => 2],
                ['label_key' => 'product_detail.bolster_size', 'unit' => 'mm', 'values' => ['680*300*70', '800*400*70', '850*440*80', '900*500*80', '1000*550*90', '1150*600*110', '1250*800*140'], 'span' => 2],
                ['label_key' => 'product_detail.throat_depth', 'unit' => 'mm', 'values' => ['155~1250', '205~1250', '225~1250', '255~1250', '280~1250', '305~1250', '405~1250'], 'span' => 2],
                ['label_key' => 'product_detail.spec_platform_floor', 'unit' => 'mm', 'values' => [795, 790, 790, 785, 830, 830, 900], 'span' => 2],
                ['label_key' => 'product_detail.spec_shank_hole', 'unit' => 'mm', 'values' => ['Φ38.1', 'Φ38.1', 'Φ38.1', 'Φ50', 'Φ50', 'Φ50', 'Φ65'], 'span' => 2],
                ['label_key' => 'product_detail.spec_motor', 'unit' => 'kW*P', 'values' => ['3.7*4', '3.7*4', '5.5*4', '5.5*4', '7.5*4', '11*4', '15*4'], 'span' => 2],
                ['label_key' => 'product_detail.spec_adjust_device', 'unit' => '/', 'values' => ['Manual', 'Manual', 'Manual', 'Manual', 'Electric / Motorizado', 'Electric / Motorizado', 'Electric / Motorizado'], 'span' => 2],
                ['label_key' => 'product_detail.spec_air_pressure', 'unit' => 'kg/cm²', 'values' => [6, 6, 6, 6, 6, 6, 6], 'span' => 2],
                ['label_key' => 'product_detail.spec_accuracy_grade', 'unit' => 'Grade', 'values' => ['JIS 1', 'JIS 1', 'JIS 1', 'JIS 1', 'JIS 1', 'JIS 1', 'JIS 1'], 'span' => 2],
                ['label_key' => 'product_detail.spec_dimension', 'unit' => 'mm', 'values' => ['1280*850*2200', '1380*900*2400', '1575*950*2500', '1595*1000*2800', '1800*1180*2980', '1900*1300*3200', '2315*1400*3670'], 'span' => 2],
                ['label_key' => 'product_detail.spec_weight', 'unit' => 'Tons', 'values' => [2.1, 3.0, 3.8, 5.6, 6.5, 9.6, 16.0], 'span' => 2],
                ['label_key' => 'product_detail.spec_die_cushion_capacity', 'unit' => 'Ton', 'values' => ['/', 2.3, 2.3, 3.6, 3.6, 6.3, 10.0], 'span' => 2],
                ['label_key' => 'product_detail.spec_die_cushion_stroke', 'unit' => 'mm', 'values' => ['/', 50, 50, 70, 70, 80, 80], 'span' => 2],
                ['label_key' => 'product_detail.spec_die_cushion_area', 'unit' => 'mm²', 'values' => ['/', '300*230', '300*230', '350*300', '450*310', '500*350', '650*420'], 'span' => 2],
            ]
        ];
    }

    public function getCatalog(): array
    {
        return $this->catalog;
    }

    public function getProductBySlug(string $slug): ?array
    {
        return $this->catalog[$slug] ?? null;
    }
}
