<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Solu��es em Gest�o P�blica                                *
    * @copyright (c) 2013 Confedera��o Nacional de Munic�pos                         *
    * @author Confedera��o Nacional de Munic�pios                                    *
    *                                                                                *
    * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo  sob *
    * os termos da Licen�a P�blica Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio) qualquer vers�o *
    *                                                                                *
    * Este  programa  �  distribu�do  na  expectativa  de  que  seja  �til,   por�m, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia impl�cita  de  COMERCIABILIDADE  OU *
    * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral  do  GNU  junto  com *
    * este programa; se n�o, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de mapeamento do arquivo CVC.inc.php
    * Data de Cria��o:  27/01/2014

    * @author Analista: Sergio
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCEMGArquivoMensalCVC.class.php 61030 2014-12-01 17:27:56Z lisiane $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGArquivoMensalCVC extends Persistente
{
    public function TTCEMGArquivoMensalCVC() {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaVeiculos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao=""){
        return $this->executaRecupera("montaRecuperaVeiculos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaVeiculos()
    {
        $stSql = "
            SELECT tipo_registro               
                        , cod_orgao
                        , (lpad(lpad(COALESCE(uniorcam_cod_orgao, 0)::VARCHAR, 2, '0')||lpad(COALESCE(uniorcam_cod_unidade,0)::VARCHAR, 2, '0'),5,'0')) AS cod_unidade_sub
                        , cod_veiculo
                        , LPAD(veiculos.tipo_veiculo::VARCHAR,2,'0') AS tipo_veiculo
                        , subtipo_veiculo
                        , TRIM(descricao) AS descricao
                        , marca
                        , modelo
                        , ano_fabricacao
                        , CASE WHEN veiculos.tipo_veiculo = 3 THEN 
                                placa
                            ELSE
                                ''::VARCHAR
                        END as placa
                        , CASE WHEN veiculos.tipo_veiculo = 3 THEN 
                                chassi
                            ELSE
                                ''::VARCHAR
                        END as chassi
                        , CASE WHEN veiculos.tipo_veiculo = 3 THEN 
                                numero_renavam
                            ELSE
                                ''::VARCHAR
                        END as numero_renavam                        
                        , numero_serie
                        , situacao_veiculo
                        , tipo_deslocamento
                FROM (
                                SELECT 10 AS tipo_registro
                                            , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                                                    orgao_bem.cod_orgao
                                              ELSE
                                                    orgao_terceiro.cod_orgao
                                              END AS cod_orgao_orc
                                            , veiculo.cod_veiculo
                                            , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                                                    descricao_veiculo.descricao
                                               ELSE
                                                    modelo.nom_modelo || ' ' || veiculo.ano_fabricacao || ' cor ' || veiculo.cor || ' placa ' || veiculo.placa
                                               END AS descricao
                                            , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                                                    orgao_bem.exercicio
                                                ELSE
                                                    orgao_terceiro.exercicio
                                                END AS exercicio
                                            , tipo_veiculo_vinculo.cod_tipo_tce AS tipo_veiculo
                                            , tipo_veiculo_vinculo.cod_subtipo_tce AS subtipo_veiculo
                                            , modelo.nom_modelo AS modelo
                                            , marca.nom_marca AS marca
                                            , veiculo.num_certificado as numero_renavam
                                            , veiculo.ano_fabricacao
                                            , SUBSTR(veiculo.placa, 1, 3) || ' ' || SUBSTR(veiculo.placa, 4, 4) AS placa
                                            , veiculo.chassi
                                            , '' AS numero_serie
                                            , CASE WHEN (terceiros.cod_veiculo is NOT NULL) THEN
                                                    02
                                                ELSE
                                              CASE WHEN (proprio.cod_veiculo is NOT NULL) THEN
                                                    01
                                                ELSE
                                                    03
                                                END
                                                END AS situacao_veiculo
                                            , 01 AS tipo_deslocamento
                                            , CASE WHEN descricao_veiculo.num_orgao IS NOT NULL THEN 
                                                descricao_veiculo.num_orgao 
                                             WHEN veiculo_uniorcam.num_orgao IS NOT NULL THEN
                                                veiculo_uniorcam.num_orgao 
                                            END as uniorcam_cod_orgao
                                            , CASE WHEN descricao_veiculo.num_unidade IS NOT NULL THEN 
                                                descricao_veiculo.num_unidade 
                                             WHEN veiculo_uniorcam.num_unidade IS NOT NULL THEN
                                                veiculo_uniorcam.num_unidade 
                                             END as uniorcam_cod_unidade
                                            , orgao_sicom.cod_entidade as cod_unidade_sub
                                            , orgao_sicom.valor as cod_orgao
                                    FROM frota.veiculo
                                       JOIN frota.modelo
                                        ON modelo.cod_modelo = veiculo.cod_modelo
                                      AND modelo.cod_marca = veiculo.cod_marca
                                      
                                LEFT JOIN patrimonio.veiculo_uniorcam
                                         ON veiculo_uniorcam.cod_veiculo = veiculo.cod_veiculo
                                        
                              LEFT JOIN frota.marca
                                        ON marca.cod_marca = modelo.cod_marca
                                      JOIN frota.veiculo_propriedade
                                       ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                     AND veiculo_propriedade.\"timestamp\" = ( SELECT MAX(vp.\"timestamp\")
                                                         FROM frota.veiculo_propriedade as vp
                                                        WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo
                                                     )
                              LEFT JOIN ( SELECT bem.descricao
                                                         , bem.cod_bem
                                                         , bem_comprado.num_orgao
                                                         , bem_comprado.num_unidade
                                                         , veiculo_propriedade.cod_veiculo
                                                         , MAX(veiculo_propriedade.\"timestamp\")
                                                         , bem_comprado.cod_entidade
                                                 FROM frota.veiculo
                                                   JOIN frota.veiculo_propriedade
                                                     ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                                   AND veiculo_propriedade.proprio = true
                                                   JOIN frota.proprio
                                                    ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                                                  AND proprio.\"timestamp\" = veiculo_propriedade.\"timestamp\"
                                          LEFT JOIN frota.terceiros
                                                   ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                                                 AND proprio.\"timestamp\" = veiculo_propriedade.\"timestamp\"
                                                 JOIN patrimonio.bem
                                                   ON bem.cod_bem = proprio.cod_bem
                                                 JOIN patrimonio.bem_comprado
                                                  ON bem_comprado.cod_bem = bem.cod_bem                      
                                       GROUP BY bem.descricao
                                                     , veiculo_propriedade.cod_veiculo
                                                     , bem.cod_bem
                                                     , bem_comprado.cod_entidade
                                                     , bem_comprado.num_orgao
                                                     , bem_comprado.num_unidade
                                                ) AS descricao_veiculo
                                            ON descricao_veiculo.cod_veiculo = veiculo.cod_veiculo
                                            
                                   LEFT JOIN ( SELECT * FROM(
                                                                                SELECT historico_bem.cod_bem
                                                                                     , historico_bem.cod_orgao
                                                                                     , CAST(EXTRACT(YEAR FROM MAX(historico_bem.timestamp)) AS VARCHAR) AS exercicio
                                                                                     , timestamp
                                                                                  FROM patrimonio.historico_bem
                                                                                 WHERE timestamp = ( SELECT MAX(timestamp) from patrimonio.historico_bem hb where hb.cod_bem = historico_bem.cod_bem )
                                                                              GROUP BY historico_bem.cod_bem
                                                                                     , historico_bem.cod_orgao
                                                                                     , timestamp
                                                                                ) as tabela WHERE exercicio <= '".Sessao::getExercicio()."'
                                               ) orgao_bem
                                           ON orgao_bem.cod_bem = descricao_veiculo.cod_bem
                                           
                                  LEFT JOIN tcemg.tipo_veiculo_vinculo
                                            ON tipo_veiculo_vinculo.cod_tipo = veiculo.cod_tipo_veiculo
                                          
                                  LEFT JOIN frota.proprio
                                           ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                                          AND proprio.\"timestamp\" = veiculo_propriedade.\"timestamp\"
                                          
                                  LEFT JOIN frota.terceiros
                                           ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                                          AND terceiros.\"timestamp\" = veiculo_propriedade.\"timestamp\"
                                          
                                  LEFT JOIN ( SELECT * FROM(
                                                     SELECT terceiros_historico.cod_veiculo
                                                          , terceiros_historico.cod_orgao
                                                          , CAST(EXTRACT(YEAR FROM MAX(terceiros_historico.timestamp)) AS VARCHAR) AS exercicio
                                                          , MAX(terceiros_historico.\"timestamp\")
                                                          , 2 as cod_entidade
                                                       FROM frota.terceiros_historico
                                                   GROUP BY terceiros_historico.cod_veiculo
                                                          , terceiros_historico.cod_orgao
                                                 ) as tabela WHERE exercicio <= '".Sessao::getExercicio()."'
                                               ) orgao_terceiro
                                            ON orgao_terceiro.cod_veiculo = veiculo.cod_veiculo
                                             
                                          JOIN (SELECT valor::integer
                                                                    , configuracao_entidade.exercicio
                                                                    , configuracao_entidade.cod_entidade
                                                                 FROM tcemg.orgao
                                                           INNER JOIN administracao.configuracao_entidade
                                                                  ON configuracao_entidade.valor::integer = orgao.num_orgao
                                                               WHERE configuracao_entidade.cod_entidade IN(".$this->getDado('entidades').") AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                                               )  AS orgao_sicom
                                           ON orgao_sicom.exercicio= '".Sessao::getExercicio()."'
                                         AND orgao_sicom.cod_entidade = orgao_terceiro.cod_entidade
                                           OR orgao_sicom.cod_entidade = descricao_veiculo.cod_entidade
                
                               ORDER BY veiculo.cod_veiculo
                     ) AS veiculos ";
        return $stSql;
    }

    public function recuperaGastosVeiculos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaGastosVeiculos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaGastosVeiculos()
    {
        $stSql = "
                Select cod_veiculo
                    , tipo_registro
                    , cod_orgao
                    , (lpad(lpad(COALESCE(uniorcam_cod_orgao, 0)::VARCHAR, 2, '0')||lpad(COALESCE(uniorcam_cod_unidade,0)::VARCHAR, 2, '0'),5,'0')) AS cod_unidade_sub
                    , origem_gasto
                    , cod_unidade_subempenho
                    , nro_empenho
                    , dt_empenho
                    , coalesce((select COALESCE (km,0) AS km
                                 from frota.manutencao kmini
                                 --DATAINICIO
                                where kmini.dt_manutencao <=  TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                  and kmini.cod_veiculo    = tabela.cod_veiculo
                                  and km > 0
                                order by  exercicio desc, cod_manutencao desc, dt_manutencao desc limit 1
                        ),0) AS marcacao_inicial
                    , coalesce((select  coalesce(km,0) as km
                                 from frota.manutencao kmfim
                                where kmfim.dt_manutencao <=  TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                  and kmfim.cod_veiculo    = tabela.cod_veiculo
                                  and km > 0

                                order by  exercicio desc, cod_manutencao desc, dt_manutencao desc limit 1
                        ),0) marcacao_final
                    , tipo_gasto
                    , TRIM(REPLACE(TO_CHAR(quantidade, '999999999.9999'),'.',',')) AS qtde_utilizada 
                    , REPLACE(valor::varchar,'.',',') AS vl_gasto 
                    , dsc_pecas_servicos
                    , atestado_controle


                 FROM  (
                       SELECT 20 AS tipo_registro 
                              , CASE WHEN descricao_veiculo.num_orgao IS NOT NULL THEN 
                                    descricao_veiculo.num_orgao 
                                 WHEN veiculo_uniorcam.num_orgao IS NOT NULL THEN
                                    veiculo_uniorcam.num_orgao 
                                END as uniorcam_cod_orgao
                              , CASE WHEN descricao_veiculo.num_unidade IS NOT NULL THEN 
                                    descricao_veiculo.num_unidade 
                                WHEN veiculo_uniorcam.num_unidade IS NOT NULL THEN
                                    veiculo_uniorcam.num_unidade 
                                END as uniorcam_cod_unidade
                           , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                                   orgao_bem.cod_orgao
                              ELSE
                                   orgao_terceiro.cod_orgao
                               END AS cod_orgao_orcamentaria

                            , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                                   orgao_bem.exercicio
                              ELSE
                                   orgao_terceiro.exercicio
                              END AS exercicio

                            , CASE WHEN (manutencao.cod_manutencao = manutencao_empenho.cod_manutencao)  THEN
                                       2
                              ELSE
                                       1
                              END AS origem_gasto

                            , CASE WHEN (item.cod_tipo = 2) THEN
                                       08
                               ELSE
                                   CASE WHEN (item.cod_tipo = 3) THEN
                                       09
                                   ELSE
                                       CASE WHEN (item.cod_tipo = 4) THEN
                                               99
                                       ELSE
                                               item.cod_tipo
                                       END
                                   END
                               END AS tipo_gasto

                            , CASE WHEN (item.cod_tipo = 2 OR item.cod_tipo = 3 OR item.cod_tipo = 4) THEN
                                       catalogo_item.descricao_resumida
                              END AS dsc_pecas_servicos

                            , manutencao.cod_veiculo
                            , manutencao_empenho.cod_empenho as nro_empenho
                            , to_char(empenho.dt_empenho::timestamp,'ddmmyyyy') as dt_empenho
                            , lpad(lpad(despesa.num_orgao::VARCHAR, 2, '0')||lpad(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_subempenho
                            , round(manutencao_item.quantidade) AS quantidade
                            , manutencao_item.valor AS valor
                            , 2 AS atestado_controle
                            , orgao_sicom.valor AS cod_orgao
                            , manutencao.cod_manutencao As cod_manutencao
                            , manutencao.exercicio As exercicio_manutencao

                         FROM frota.manutencao
                         JOIN frota.veiculo_propriedade
                           ON veiculo_propriedade.cod_veiculo = manutencao.cod_veiculo
                          AND veiculo_propriedade.\"timestamp\" = ( SELECT MAX(vp.\"timestamp\")
                                                                      FROM frota.veiculo_propriedade as vp
                                                                     WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo
                                                                  )
                    LEFT JOIN ( SELECT  bem.cod_bem
                                      , veiculo_propriedade.cod_veiculo
                                      , MAX(veiculo_propriedade.\"timestamp\")
                                      , bem_comprado.cod_entidade
                                      , bem_comprado.num_orgao
                                      , bem_comprado.num_unidade
                                   FROM frota.veiculo
                                   JOIN frota.veiculo_propriedade
                                     ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                    AND veiculo_propriedade.proprio = true
                                   JOIN frota.proprio
                                     ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                                    AND proprio.\"timestamp\" = veiculo_propriedade.\"timestamp\"
                                   JOIN patrimonio.bem
                                     ON bem.cod_bem = proprio.cod_bem
                                   JOIN patrimonio.bem_comprado
                                     ON bem_comprado.cod_bem = bem.cod_bem
                               GROUP BY veiculo_propriedade.cod_veiculo
                                      , bem.cod_bem
                                      , bem_comprado.cod_entidade
                                      , bem_comprado.num_orgao
                                      , bem_comprado.num_unidade
                               ) AS descricao_veiculo
                           ON descricao_veiculo.cod_veiculo = manutencao.cod_veiculo
                    LEFT JOIN ( SELECT * FROM (
                                                SELECT historico_bem.cod_bem
                                                     , historico_bem.cod_orgao
                                                     , CAST(EXTRACT(YEAR FROM MAX(historico_bem.timestamp)) AS VARCHAR) AS exercicio
                                                     , timestamp
                                                  FROM patrimonio.historico_bem
                                                 WHERE timestamp = ( SELECT MAX(timestamp) from patrimonio.historico_bem hb where hb.cod_bem = historico_bem.cod_bem )
                                     GROUP BY historico_bem.cod_bem
                                            , historico_bem.cod_orgao
                                            , timestamp
                                    ) as tabela WHERE exercicio <= '".Sessao::getExercicio()."'
                                ) orgao_bem
                           ON orgao_bem.cod_bem = descricao_veiculo.cod_bem

                    LEFT JOIN ( SELECT * FROM (
                                                SELECT terceiros_historico.cod_veiculo
                                                     , terceiros_historico.cod_orgao
                                                     , CAST(EXTRACT(YEAR FROM MAX(terceiros_historico.timestamp)) AS VARCHAR) AS exercicio
                                                     , MAX(terceiros_historico.\"timestamp\")
                                                     , 2 as cod_entidade
                                                  FROM frota.terceiros_historico
                                     GROUP BY terceiros_historico.cod_veiculo
                                            , terceiros_historico.cod_orgao
                                     ) as tabela where exercicio <= '".Sessao::getExercicio()."'
                               ) orgao_terceiro
                           ON orgao_terceiro.cod_veiculo = manutencao.cod_veiculo

                    LEFT JOIN frota.efetivacao
                           ON efetivacao.cod_manutencao = manutencao.cod_manutencao
                          AND manutencao.exercicio = efetivacao.exercicio_manutencao
                    LEFT JOIN frota.autorizacao
                           ON autorizacao.cod_autorizacao = efetivacao.cod_autorizacao
                          AND autorizacao.exercicio = efetivacao.exercicio_autorizacao
                         JOIN frota.manutencao_item
                           ON manutencao_item.cod_manutencao = manutencao.cod_manutencao
                          AND manutencao_item.exercicio = manutencao.exercicio
                         JOIN frota.item
                           ON item.cod_item = manutencao_item.cod_item

                    LEFT JOIN almoxarifado.catalogo_item
                           ON catalogo_item.cod_item = manutencao_item.cod_item
                    LEFT JOIN (Select cod_empenho
                                     , exercicio
                                     , cod_manutencao
                                     , cod_entidade
                                FROM frota.manutencao_empenho) as manutencao_empenho
                           ON manutencao_empenho.cod_manutencao = manutencao.cod_manutencao
                          AND manutencao_empenho.exercicio = manutencao.exercicio
                    LEFT JOIN empenho.empenho
                           ON empenho.cod_empenho = manutencao_empenho.cod_empenho
                          AND empenho.exercicio = manutencao_empenho.exercicio
                          AND empenho.cod_entidade = manutencao_empenho.cod_entidade
                    LEFT JOIN empenho.pre_empenho_despesa
                           ON pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
                          AND pre_empenho_despesa.exercicio  = empenho.exercicio
                    LEFT JOIN orcamento.despesa
                           ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                          AND pre_empenho_despesa.exercicio  = despesa.exercicio
                    LEFT JOIN (SELECT valor::integer
                                    , configuracao_entidade.exercicio
                                    , configuracao_entidade.cod_entidade
                                 FROM tcemg.orgao
                           INNER JOIN administracao.configuracao_entidade
                                  ON configuracao_entidade.valor::integer = orgao.num_orgao
                               WHERE configuracao_entidade.cod_entidade IN(".$this->getDado('entidades').")  AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                              )  AS orgao_sicom
                           ON orgao_sicom.exercicio='".Sessao::getExercicio()."'
                          AND orgao_sicom.cod_entidade = orgao_terceiro.cod_entidade
                           OR orgao_sicom.cod_entidade = descricao_veiculo.cod_entidade
                  LEFT JOIN patrimonio.veiculo_uniorcam
                            ON veiculo_uniorcam.cod_veiculo = manutencao.cod_veiculo
                  WHERE manutencao.dt_manutencao between TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')  

                    GROUP BY manutencao.cod_veiculo
                              , veiculo_propriedade.proprio
                              , uniorcam_cod_orgao
                              , uniorcam_cod_unidade
                              , orgao_bem.cod_orgao
                              , orgao_terceiro.cod_orgao
                              , orgao_bem.exercicio
                              , orgao_terceiro.exercicio
                              , efetivacao.cod_manutencao
                              , manutencao_empenho.cod_manutencao
                              , empenho.dt_empenho
                              , manutencao_empenho.cod_empenho
                              , despesa.num_orgao
                              , despesa.num_unidade
                              , item.cod_tipo
                              , manutencao_item.quantidade
                              , manutencao_item.valor
                              , catalogo_item.descricao_resumida
                              , orgao_sicom.valor
                              , manutencao.cod_manutencao
                              , manutencao.exercicio
                    ORDER BY manutencao.cod_veiculo
               )as tabela";
              
        return $stSql;
    }
    
    public function recuperaCVC30(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCVC30",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCVC30()
    {
        $stSql = "
          
              Select tipo_registro
                        , cod_veiculo
                        , cod_orgao
                        , (lpad(lpad(COALESCE(uniorcam_cod_orgao, 0)::VARCHAR, 2, '0')||lpad(COALESCE(uniorcam_cod_unidade,0)::VARCHAR, 2, '0'),5,'0')) AS cod_unidade_sub
                        , nome_estabelecimento
                        , localidade
                        , qtde_dias_rodados
                        , distacia_estabelecimento     
                        , numero_passageiros
                        , turnos
                FROM ( 
                          SELECT 30 AS tipo_registro 
                                    , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                                        orgao_bem.cod_orgao
                                      ELSE
                                        orgao_terceiro.cod_orgao
                                      END AS cod_orgao_orcamentaria
                                    , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                                        orgao_bem.exercicio
                                      ELSE
                                        orgao_terceiro.exercicio
                                      END AS veic_exercicio
                                   , CASE WHEN descricao_veiculo.num_orgao IS NOT NULL THEN 
                                        descricao_veiculo.num_orgao 
                                     WHEN veiculo_uniorcam.num_orgao IS NOT NULL THEN
                                        veiculo_uniorcam.num_orgao 
                                    END as uniorcam_cod_orgao
                                    , CASE WHEN descricao_veiculo.num_unidade IS NOT NULL THEN 
                                    descricao_veiculo.num_unidade 
                                        WHEN veiculo_uniorcam.num_unidade IS NOT NULL THEN
                                            veiculo_uniorcam.num_unidade 
                                        END as uniorcam_cod_unidade
                                    , orgao_sicom.valor AS cod_orgao
                                    , transporte_escolar.cod_veiculo  
                                    , sw_cgm.nom_cgm as nome_estabelecimento
                                    , sw_cgm.bairro as localidade
                                    , transporte_escolar.dias_rodados as qtde_dias_rodados
                                    , transporte_escolar.distancia as distacia_estabelecimento     
                                    , transporte_escolar.passageiros as numero_passageiros
                                    , transporte_escolar.cod_turno as turnos
                               
                            FROM  frota.transporte_escolar
                    INNER JOIN   frota.veiculo
                                ON veiculo.cod_veiculo = transporte_escolar.cod_veiculo
                                
                    INNER JOIN sw_cgm
                                ON sw_cgm.numcgm = transporte_escolar.cgm_escola
                      
                             JOIN frota.veiculo_propriedade
                               ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                             AND veiculo_propriedade.\"timestamp\" = ( SELECT MAX(vp.\"timestamp\")
                                                                                                   FROM frota.veiculo_propriedade as vp
                                                                                                WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo
                                                                                                )
                      LEFT JOIN ( SELECT bem.descricao
                                      , bem.cod_bem
                                      , veiculo_propriedade.cod_veiculo
                                      , MAX(veiculo_propriedade.\"timestamp\")
                                      , bem_comprado.cod_entidade
                                      , bem_comprado.num_orgao
                                      , bem_comprado.num_unidade
                                   FROM frota.veiculo
                                   JOIN frota.veiculo_propriedade
                                     ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                    AND veiculo_propriedade.proprio = true
                                   JOIN frota.proprio
                                     ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                                    AND proprio.\"timestamp\" = veiculo_propriedade.\"timestamp\"
                              LEFT JOIN frota.terceiros
                                     ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                                    AND proprio.\"timestamp\" = veiculo_propriedade.\"timestamp\"
                                   JOIN patrimonio.bem
                                     ON bem.cod_bem = proprio.cod_bem
                                   JOIN patrimonio.bem_comprado
                                     ON bem_comprado.cod_bem = bem.cod_bem
                               GROUP BY bem.descricao
                                      , veiculo_propriedade.cod_veiculo
                                      , bem.cod_bem
                                      , bem_comprado.cod_entidade
                                      , bem_comprado.num_orgao
                                      , bem_comprado.num_unidade                                   
                             ) AS descricao_veiculo
                            ON descricao_veiculo.cod_veiculo = veiculo.cod_veiculo
                    LEFT JOIN (  SELECT *
                                         FROM
                                         (
                                            SELECT historico_bem.cod_bem
                                                      , historico_bem.cod_orgao
                                                      , CAST(EXTRACT(YEAR FROM MAX(historico_bem.timestamp)) AS VARCHAR) AS exercicio
                                                      , timestamp
                                              FROM patrimonio.historico_bem
                                            WHERE timestamp = ( SELECT MAX(timestamp) from patrimonio.historico_bem hb where hb.cod_bem = historico_bem.cod_bem )
                                       GROUP BY historico_bem.cod_bem
                                                     , historico_bem.cod_orgao
                                                     , timestamp
                                      ORDER BY historico_bem.cod_bem
                                        ) as tabela WHERE exercicio <= '".Sessao::getExercicio()."'
                                  ) orgao_bem
                              ON orgao_bem.cod_bem = descricao_veiculo.cod_bem
               
                    LEFT JOIN frota.proprio
                             ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                           AND proprio.\"timestamp\" = veiculo_propriedade.\"timestamp\"
                   LEFT JOIN frota.terceiros
                            ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                          AND terceiros.\"timestamp\"= veiculo_propriedade.\"timestamp\"
                             
                  LEFT JOIN ( SELECT * 
                                      FROM (
                                                    SELECT terceiros_historico.cod_veiculo
                                                              , terceiros_historico.cod_orgao
                                                              , CAST(EXTRACT(YEAR FROM MAX(terceiros_historico.timestamp)) AS VARCHAR) AS exercicio
                                                              , MAX(terceiros_historico.\"timestamp\")
                                                              , 2 as cod_entidade
                                                      FROM frota.terceiros_historico
                                               GROUP BY terceiros_historico.cod_veiculo
                                                             , terceiros_historico.cod_orgao
                                                ) as tabela WHERE exercicio <= '".Sessao::getExercicio()."'
                              ) orgao_terceiro
                          ON orgao_terceiro.cod_veiculo = veiculo.cod_veiculo

                     INNER JOIN (SELECT valor::integer
                                          , configuracao_entidade.exercicio
                                          , configuracao_entidade.cod_entidade
                                  FROM tcemg.orgao
                          INNER JOIN administracao.configuracao_entidade
                                      ON configuracao_entidade.valor::integer = orgao.num_orgao
                                WHERE configuracao_entidade.cod_entidade IN (".$this->getDado('entidades').")   AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                             )  AS orgao_sicom
                         ON orgao_sicom.exercicio= '".Sessao::getExercicio()."'
                       AND orgao_sicom.cod_entidade = orgao_terceiro.cod_entidade
                         OR orgao_sicom.cod_entidade = descricao_veiculo.cod_entidade
                      LEFT JOIN patrimonio.veiculo_uniorcam
                            ON veiculo_uniorcam.cod_veiculo = veiculo.cod_veiculo
      
             GROUP BY tipo_registro 
                                     , uniorcam_cod_orgao
                                    , uniorcam_cod_unidade
                                    ,  cod_orgao_orcamentaria
                                    , veic_exercicio
                                    , orgao_sicom.valor
                                    , transporte_escolar.cod_veiculo  
                                    , nome_estabelecimento
                                    , localidade
                                    , qtde_dias_rodados
                                    , distacia_estabelecimento     
                                    , numero_passageiros
                                    , turnos
             ORDER BY transporte_escolar.cod_veiculo  
                    )  as tabela 
                ";

        return $stSql;
    }

    
    function recuperaVeiculosBaixados(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaVeiculosBaixados",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaVeiculosBaixados()
    {
        $stSql = " Select tipo_registro
                        , cod_veiculo
                        , uniorcam_cod_orgao
                        , (lpad(lpad(COALESCE(uniorcam_cod_orgao, 0)::VARCHAR, 2, '0')||lpad(COALESCE(uniorcam_cod_unidade,0)::VARCHAR, 2, '0'),5,'0')) AS cod_unidade_sub
                        , cod_tipo
                        , dt_baixa
                        , TRIM(descbaixa) AS descbaixa

     
                    FROM( 
                          SELECT    40 AS tipo_registro 
                               ,    CASE WHEN (veiculo_propriedade.proprio = true) THEN
                                        orgao_bem.cod_orgao
                                    ELSE
                                        orgao_terceiro.cod_orgao
                                    END AS cod_orgao_orcamentaria
                                    
                                ,   CASE WHEN (veiculo_propriedade.proprio = true) THEN
                                        orgao_bem.exercicio
                                    ELSE
                                        orgao_terceiro.exercicio
                                    END AS exercicio
                                
                                ,   CASE WHEN (tipo_baixa.cod_tipo = 99) THEN            
                                        veiculo_baixado.motivo
                                    END as descbaixa
                                ,   veiculo_baixado.cod_veiculo as cod_veiculo
                                ,   orgao_sicom.valor AS cod_orgao
                                ,   to_char(veiculo_baixado.dt_baixa,'ddmmyyyy')as dt_baixa
                                ,   tipo_baixa.cod_tipo as cod_tipo
                                , CASE WHEN descricao_veiculo.num_orgao IS NOT NULL THEN 
                                        descricao_veiculo.num_orgao 
                                   WHEN veiculo_uniorcam.num_orgao IS NOT NULL THEN
                                        veiculo_uniorcam.num_orgao 
                                    END as uniorcam_cod_orgao
                               , CASE WHEN descricao_veiculo.num_unidade IS NOT NULL THEN 
                                    descricao_veiculo.num_unidade 
                                WHEN veiculo_uniorcam.num_unidade IS NOT NULL THEN
                                    veiculo_uniorcam.num_unidade 
                                END as uniorcam_cod_unidade
                            FROM    frota.veiculo
                            JOIN    frota.veiculo_baixado
                              ON    veiculo.cod_veiculo = veiculo_baixado.cod_veiculo
                       LEFT JOIN    frota.tipo_baixa
                              ON    tipo_baixa.cod_tipo = veiculo_baixado.cod_tipo_baixa 
                            JOIN    frota.veiculo_propriedade
                              ON    veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                             AND    veiculo_propriedade.\"timestamp\" = ( SELECT MAX(vp.\"timestamp\")
                                                                                        FROM frota.veiculo_propriedade as vp
                                                                                       WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo
                                                                        )
                       LEFT JOIN ( SELECT   bem.descricao
                                        ,   bem.cod_bem
                                        ,   veiculo_propriedade.cod_veiculo
                                        ,   MAX(veiculo_propriedade.\"timestamp\")
                                        ,   bem_comprado.cod_entidade
                                        , bem_comprado.num_orgao
                                        , bem_comprado.num_unidade
                                    FROM    frota.veiculo_baixado
                                    JOIN    frota.veiculo_propriedade
                                      ON    veiculo_propriedade.cod_veiculo = veiculo_baixado.cod_veiculo
                                     AND    veiculo_propriedade.proprio = true
                                    JOIN    frota.proprio
                                      ON    proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
               
                    
                                    JOIN    patrimonio.bem
                                      ON    bem.cod_bem = proprio.cod_bem
                                    JOIN    patrimonio.bem_comprado
                                      ON    bem_comprado.cod_bem = bem.cod_bem
                                GROUP BY    bem.descricao
                                       ,    veiculo_propriedade.cod_veiculo
                                       ,    bem.cod_bem
                                       ,    bem_comprado.cod_entidade
                                       , bem_comprado.num_orgao
                                       , bem_comprado.num_unidade
                                ) AS descricao_veiculo
                              ON descricao_veiculo.cod_veiculo = veiculo_baixado.cod_veiculo

                       LEFT JOIN (  SELECT *
                                      FROM
                                         (
                                            SELECT historico_bem.cod_bem
                                                 , historico_bem.cod_orgao
                                                 , CAST(EXTRACT(YEAR FROM MAX(historico_bem.timestamp)) AS VARCHAR) AS exercicio
                                                 , timestamp
                                              FROM patrimonio.historico_bem
                                              WHERE timestamp = ( SELECT MAX(timestamp) from patrimonio.historico_bem hb where hb.cod_bem = historico_bem.cod_bem )
                                          GROUP BY historico_bem.cod_bem
                                                 , historico_bem.cod_orgao
                                                 , timestamp
                                          ORDER BY historico_bem.cod_bem
                                         ) as tabela WHERE exercicio <= '".Sessao::getExercicio()."' 
                                ) orgao_bem
                              ON orgao_bem.cod_bem = descricao_veiculo.cod_bem
               
                       LEFT JOIN frota.proprio
                              ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                             AND proprio.\"timestamp\" = veiculo_propriedade.\"timestamp\"
                       LEFT JOIN frota.terceiros
                              ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                             AND terceiros.\"timestamp\"= veiculo_propriedade.\"timestamp\"
                             
                       LEFT JOIN ( SELECT * FROM(
                                            SELECT terceiros_historico.cod_veiculo
                                                  , terceiros_historico.cod_orgao
                                                  , CAST(EXTRACT(YEAR FROM MAX(terceiros_historico.timestamp)) AS VARCHAR) AS exercicio
                                                  , MAX(terceiros_historico.\"timestamp\")
                                                  , 2 as cod_entidade
                                               FROM frota.terceiros_historico
                                           GROUP BY terceiros_historico.cod_veiculo
                                                  , terceiros_historico.cod_orgao
                                         ) as tabela WHERE exercicio <= '".Sessao::getExercicio()."'
                                ) orgao_terceiro
                              ON orgao_terceiro.cod_veiculo = veiculo_baixado.cod_veiculo

                            JOIN (SELECT valor::integer
                                        , configuracao_entidade.exercicio
                                        , configuracao_entidade.cod_entidade
                                    FROM tcemg.orgao
                              INNER JOIN administracao.configuracao_entidade
                                      ON configuracao_entidade.valor::integer = orgao.num_orgao
                                   WHERE configuracao_entidade.cod_entidade IN (".$this->getDado('entidades').")   AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                                  )  AS orgao_sicom
                              ON orgao_sicom.exercicio='".Sessao::getExercicio()."'
                             AND orgao_sicom.cod_entidade = orgao_terceiro.cod_entidade
                              OR orgao_sicom.cod_entidade = descricao_veiculo.cod_entidade
                   LEFT JOIN patrimonio.veiculo_uniorcam
                            ON veiculo_uniorcam.cod_veiculo = veiculo.cod_veiculo
      
                           WHERE veiculo_baixado.dt_baixa between TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')  
      
                        GROUP BY   veiculo_baixado.cod_veiculo
                                  , uniorcam_cod_orgao
                                  , uniorcam_cod_unidade
                                  , veiculo_propriedade.proprio
                                  , orgao_bem.cod_orgao
                                  , orgao_terceiro.cod_orgao
                                  , orgao_bem.exercicio
                                  , orgao_terceiro.exercicio
                                  , orgao_sicom.valor
                                  , veiculo_baixado.dt_baixa
                                  , veiculo_baixado.motivo
                                  , tipo_baixa.cod_tipo
                                  , orgao_terceiro.cod_entidade
                        ORDER BY veiculo_baixado.cod_veiculo
                    )  as tabela ";
        return $stSql;
    }
    
    public function __destruct(){}

}

?>