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
*/
?>
<?php
/**
    * Extensão da Classe de mapeamento
    * Data de Criação: 22/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63087 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.2  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/07/22 20:21:25  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );

/**
  *
  * Data de Criação: 22/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAMovimentoContabil extends TContabilidadePlanoConta
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAMovimentoContabil()
{
    parent::TContabilidadePlanoConta();

    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql .= " SELECT   exercicio                                         
             , cod_estrutural                                              
             , tipo_mov                                                    
             , mes                                                         
             , abs(sum(valor_credito)) as vl_credito                       
             , abs(sum(valor_debito))  as vl_debito                        
             , row_number() over (order by cod_estrutural ) AS nu_sequencial_tc 

     FROM (                                                           
         SELECT   pc.exercicio                                        
                , pc.cod_estrutural                                   
                , case when vl.tipo='I' then 1 else 3 end as tipo_mov 
                , to_char(lo.dt_lote,'mm') as mes                     
                , sum(vl.vl_lancamento) as valor_credito              
                , 0.00 as valor_debito                                
         FROM     contabilidade.plano_conta      as pc                
                , contabilidade.plano_analitica  as pa                
                , contabilidade.conta_credito    as co                
                , contabilidade.valor_lancamento as vl                
                , contabilidade.lote             as lo                
          WHERE pc.exercicio    = pa.exercicio                       
            AND pc.cod_conta    = pa.cod_conta                       
            AND pa.exercicio    = co.exercicio                       
            AND pa.cod_plano    = co.cod_plano                       
            AND co.exercicio    = vl.exercicio                       
            AND co.cod_entidade = vl.cod_entidade                    
            AND co.cod_lote     = vl.cod_lote                        
            AND co.tipo         = vl.tipo                            
            AND co.tipo_valor   = vl.tipo_valor                      
            AND co.sequencia    = vl.sequencia                       
            AND vl.exercicio    = lo.exercicio                       
            AND vl.cod_entidade = lo.cod_entidade                    
            AND vl.tipo         = lo.tipo                            
            AND vl.cod_lote     = lo.cod_lote                        
            AND pc.exercicio='".$this->getDado('exercicio')."' \n";
         
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     vl.cod_entidade IN (".$this->getDado('stEntidades').") \n";
    }
    
    
    $stSql .= " GROUP BY pc.exercicio, pc.cod_estrutural, vl.tipo, to_char(lo.dt_lote,'mm')
                                                                                        
                UNION                                                                      
                                                                                        
                SELECT   pc.exercicio                                                      
                        ,pc.cod_estrutural                                                 
                        ,case when vl.tipo='I' then 1 else 3 end as tipo_mov               
                        ,to_char(lo.dt_lote,'mm') as mes                                   
                        ,0.00 as valor_credito                                             
                        ,sum(vl.vl_lancamento) as valor_debito                             
                FROM     contabilidade.plano_conta      as pc                              
                        ,contabilidade.plano_analitica  as pa                              
                        ,contabilidade.conta_debito     as co                              
                        ,contabilidade.valor_lancamento as vl                              
                        ,contabilidade.lote             as lo                              
                WHERE   pc.exercicio    = pa.exercicio                                     
                AND     pc.cod_conta    = pa.cod_conta                                     
                AND     pa.exercicio    = co.exercicio                                     
                AND     pa.cod_plano    = co.cod_plano                                     
                AND     co.exercicio    = vl.exercicio                                     
                AND     co.cod_entidade = vl.cod_entidade                                  
                AND     co.cod_lote     = vl.cod_lote                                      
                AND     co.tipo         = vl.tipo                                          
                AND     co.tipo_valor   = vl.tipo_valor                                    
                AND     co.sequencia    = vl.sequencia                                     
                AND     vl.exercicio    = lo.exercicio                                     
                AND     vl.cod_entidade = lo.cod_entidade                                  
                AND     vl.tipo         = lo.tipo                                          
                AND     vl.cod_lote     = lo.cod_lote                                      
                AND     pc.exercicio='".$this->getDado('exercicio')."' \n";
                
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     vl.cod_entidade IN (".$this->getDado('stEntidades').") \n";
    }
    
    $stSql .= "     GROUP BY pc.exercicio, pc.cod_estrutural, vl.tipo, to_char(lo.dt_lote,'mm')
                 ) as tabela                                                                    
                 
                 GROUP BY exercicio, cod_estrutural, tipo_mov, mes                              
                 ORDER BY exercicio, cod_estrutural, tipo_mov, mes \n";

    return $stSql;
}

}
