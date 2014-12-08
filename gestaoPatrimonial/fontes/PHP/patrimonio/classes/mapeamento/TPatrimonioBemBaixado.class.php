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
    * Data de Criação: 13/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 25841 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-05 10:02:21 -0300 (Sex, 05 Out 2007) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.2  2007/10/05 13:00:16  hboaventura
inclusão dos arquivos

Revision 1.1  2007/09/18 15:10:55  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPatrimonioBemBaixado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPatrimonioBemBaixado()
{
    parent::Persistente();
    $this->setTabela('patrimonio.bem_baixado');
    $this->setCampoCod('cod_bem');
    $this->AddCampo('cod_bem','integer',true,'',true,true);
    $this->AddCampo('dt_baixa','date',true,'',false,false);
    $this->AddCampo('motivo','text',true,'',false,false);

}

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT  bem.cod_bem
                 ,  bem.cod_natureza
                 ,  bem.cod_grupo
                 ,  bem.cod_especie
                 ,  bem.descricao
                 ,  TO_CHAR(bem_baixado.dt_baixa,'dd/mm/yyyy') AS dt_baixa
                 ,  bem_baixado.motivo
                 ,  CASE WHEN ( bem_baixado.cod_bem IS NOT NULL )
                         THEN 'baixado'
                         ELSE NULL
                    END AS status
              FROM  patrimonio.bem
         LEFT JOIN  patrimonio.bem_baixado
                ON  bem_baixado.cod_bem = bem.cod_bem
             WHERE ";
        if ( $this->getDado( 'cod_bem' ) ) {
            $stSql.= " bem.cod_bem = ".$this->getDado('cod_bem')."  AND  ";
        }

        return substr($stSql,0,-6);
    }

}
