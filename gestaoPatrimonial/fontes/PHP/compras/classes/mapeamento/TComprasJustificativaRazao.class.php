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
    * @author Analista: Gelson Goncalves
    * @author Desenvolvedor: Lisiane Morais
    * $Id: TComprasJustificativaRazao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TComprasJustificativaRazao extends Persistente
{
    public function TComprasJustificativaRazao()
    {
        parent::Persistente();
        $this->setTabela("compras.justificativa_razao");

        $this->setCampoCod('cod_compra_direta');
        $this->setComplementoChave('cod_entidade,exercicio_entidade,cod_modalidade');

        $this->AddCampo( 'cod_compra_direta'	,'integer'     , true	, ''	,true	,false  );
        $this->AddCampo( 'cod_entidade'       	,'integer'	, true	, '' 	,true  	,true	);
        $this->AddCampo( 'cod_modalidade'   	,'integer'      , true	, '' 	,true  	,true   );
        $this->AddCampo( 'exercicio_entidade'   ,'char'		, true	, '4' 	,true  	,true	);
        $this->AddCampo( 'justificativa'        ,'varchar'      , false , '250' ,false  ,false  );
        $this->AddCampo( 'razao'                ,'varchar'      , false , '250' ,false  ,false  );

    }




}