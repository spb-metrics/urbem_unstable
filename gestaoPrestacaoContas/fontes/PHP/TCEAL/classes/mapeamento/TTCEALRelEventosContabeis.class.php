<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************

    * Extensão da Classe de Mapeamento TTCEALBemVinculado
    *
    * Data de Criação: 28/05/2014
    *
    * @author: Carlos Adriano Vernieri da Silva
    *
    * $Id: TTCEALBemVinculado.class.php 61420 2015-01-15 16:24:25Z jean $
    *
    * @ignore
    *
*/
class TTCEALRelEventosContabeis extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALRelEventosContabeis()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
  
   public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
  {
    $stSql = "
                SELECT (   SELECT PJ.cnpj 
                             FROM orcamento.entidade 
                       INNER JOIN sw_cgm 
                               ON sw_cgm.numcgm = entidade.numcgm
                       INNER JOIN sw_cgm_pessoa_juridica AS PJ 
                               ON PJ.numcgm = sw_cgm.numcgm
                            WHERE entidade.exercicio    = '".$this->getDado('stExercicio')."' 
                              AND entidade.cod_entidade = ".$this->getDado('inCodEntidade')."
                       ) AS cod_und_gestora
                     , (SELECT LPAD(valor,4,'0') 
                          FROM administracao.configuracao_entidade 
                         WHERE exercicio    = '".$this->getDado('stExercicio')."'
                           AND cod_entidade = ".$this->getDado('inCodEntidade')." 
                           AND cod_modulo   = 62 
                           AND parametro    = 'tceal_configuracao_unidade_autonoma'
                       ) AS codigo_ua
                     , ".$this->getDado('bimestre')." AS bimestre
                     , '".$this->getDado('stExercicio')."' AS exercicio
                     , CASE WHEN ccpc.cod_estrutural IS NOT NULL THEN SUBSTRING(REPLACE(ccpc.cod_estrutural, '.', ''), 1, 8)
                            WHEN cdpc.cod_estrutural IS NOT NULL THEN SUBSTRING(REPLACE(cdpc.cod_estrutural, '.', ''), 1, 8)
                       END AS cod_evento
                     , CASE WHEN ccpc.nom_conta IS NOT NULL THEN REPLACE(ccpc.nom_conta, '.', '')
                            WHEN cdpc.nom_conta IS NOT NULL THEN REPLACE(cdpc.nom_conta, '.', '')
                       END AS titulo_evento
                     , CASE WHEN ccpc.tipo_valor = 'C' THEN 2
                            WHEN cdpc.tipo_valor = 'D' THEN 1
                       END AS id_debcred
                     , CASE WHEN ccpc.cod_estrutural IS NOT NULL THEN REPLACE(ccpc.cod_estrutural, '.', '')
                            WHEN cdpc.cod_estrutural IS NOT NULL THEN REPLACE(cdpc.cod_estrutural, '.', '')
                       END AS cod_conta_contabil

                  FROM contabilidade.lancamento

                  JOIN contabilidade.valor_lancamento
                    ON valor_lancamento.cod_lote      = lancamento.cod_lote       
                   AND valor_lancamento.tipo          = lancamento.tipo            
                   AND valor_lancamento.sequencia     = lancamento.sequencia       
                   AND valor_lancamento.exercicio     = lancamento.exercicio       
                   AND valor_lancamento.cod_entidade  = lancamento.cod_entidade

                  JOIN contabilidade.lote
                    ON lote.exercicio    = lancamento.exercicio
                   AND lote.cod_lote     = lancamento.cod_lote
                   AND lote.tipo         = lancamento.tipo
                   AND lote.cod_entidade = lancamento.cod_entidade

            LEFT  JOIN (  SELECT                                    
                                 cc.cod_lote,                                     
                                 cc.tipo,                                         
                                 cc.sequencia,                                    
                                 cc.exercicio,                                    
                                 cc.tipo_valor,                                   
                                 cc.cod_entidade,                                 
                                 cc.cod_plano,                                    
                                 pc.cod_estrutural,  
                                 pc.nom_conta                                      
                            FROM contabilidade.plano_analitica     AS pa          
                            JOIN contabilidade.conta_credito       AS cc          
                              ON cc.cod_plano    = pa.cod_plano
                             AND cc.exercicio    = pa.exercicio                                                                                                    
                            JOIN contabilidade.plano_conta         AS pc          
                              ON pc.cod_conta    = pa.cod_conta
                             AND pc.exercicio    = pa.exercicio                                                             
                           WHERE pa.exercicio = '".$this->getDado('stExercicio')."' 
                        ) AS  ccpc                                             
                     ON ccpc.cod_lote     = valor_lancamento.cod_lote       
                    AND ccpc.sequencia    = valor_lancamento.sequencia      
                    AND ccpc.tipo_valor   = valor_lancamento.tipo_valor     
                    AND ccpc.tipo         = valor_lancamento.tipo           
                    AND ccpc.exercicio    = valor_lancamento.exercicio      
                    AND ccpc.cod_entidade = valor_lancamento.cod_entidade

              LEFT JOIN (  SELECT                                               
                                  cd.cod_lote,                                     
                                  cd.tipo,                                         
                                  cd.sequencia,                                    
                                  cd.exercicio,                                    
                                  cd.tipo_valor,                                   
                                  cd.cod_entidade,                                 
                                  cd.cod_plano,                                    
                                  pc.cod_estrutural,  
                                  pc.nom_conta                                      
                             FROM contabilidade.plano_analitica     AS pa          
                             JOIN contabilidade.conta_debito        AS cd          
                               ON cd.cod_plano    = pa.cod_plano    
                              AND cd.exercicio    = pa.exercicio                                                          
                             JOIN contabilidade.plano_conta         AS pc          
                               ON pc.cod_conta    = pa.cod_conta   
                              AND pc.exercicio    = pa.exercicio                                                           
                            WHERE pa.exercicio = '".$this->getDado('stExercicio')."' 
                        ) AS  cdpc                                             
                     ON cdpc.cod_lote     = valor_lancamento.cod_lote       
                    AND cdpc.tipo_valor   = valor_lancamento.tipo_valor     
                    AND cdpc.tipo         = valor_lancamento.tipo           
                    AND cdpc.sequencia    = valor_lancamento.sequencia      
                    AND cdpc.exercicio    = valor_lancamento.exercicio      
                    AND cdpc.cod_entidade = valor_lancamento.cod_entidade              

                 WHERE lancamento.exercicio = '".$this->getDado('stExercicio')."'
                   AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado("dt_inicial")."','dd/mm/yyyy')
                   AND TO_DATE('".$this->getDado("dt_final")."','dd/mm/yyyy')
                   AND lancamento.cod_entidade IN ( ".$this->getDado('inCodEntidade')." )

              GROUP BY 1,2,3,4,5,6,7,8
            ";
            
        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);

     }
}
?>
